<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicCard;
use App\Models\AcademicCourse;
use App\Models\AcademicReview;
use App\Models\AcademicTopic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApiAcademicController extends Controller
{
    /**
     * 1. Tüm Dersleri ve İstatistikleri Listele
     */
    public function getCourses()
    {
        $now = Carbon::now();
        $courses = AcademicCourse::withCount(['topics'])
            ->get()
            ->map(function ($course) use ($now) {
                $cardCount = AcademicCard::whereIn('topic_id', $course->topics->pluck('id'))->count();
                $dueCount = AcademicReview::whereHas('card.topic', function ($q) use ($course) {
                    $q->where('course_id', $course->id);
                })->where('next_review_at', '<=', $now)->count();

                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'code' => $course->code,
                    'topic_count' => $course->topics_count,
                    'card_count' => $cardCount,
                    'due_count' => $dueCount,
                ];
            });

        return response()->json([
            'success' => true,
            'courses' => $courses,
        ]);
    }

    /**
     * 2. Bir Derse Ait Konuları Listele
     */
    public function getTopics($courseId)
    {
        $topics = AcademicTopic::where('course_id', $courseId)
            ->withCount('cards')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'topics' => $topics,
        ]);
    }

    /**
     * 3. Bir Konunun Kartlarını Kronolojik Olarak Getir (Defter / Timeline Modu)
     */
    public function getCardsInTopic($topicId)
    {
        $cards = AcademicCard::where('topic_id', $topicId)
            ->with('review')
            ->orderBy('step_order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'cards' => $cards,
        ]);
    }

    /**
     * 4. Tekrarı Gelen Kartları Getir (Mobil Mikro Tekrar Modu)
     */
    public function getDueCards(Request $request)
    {
        $now = Carbon::now();
        $limit = $request->query('limit', 20);
        $courseName = $request->query('course');

        $query = AcademicCard::with(['topic.course', 'review'])
            ->whereHas('review', function ($q) use ($now) {
                $q->where('next_review_at', '<=', $now);
            });

        if ($courseName && $courseName !== '[Tüm Dersler (Karışık)]') {
            $query->whereHas('topic.course', function ($q) use ($courseName) {
                $q->where('name', $courseName);
            });
        }

        $cards = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'due_count' => $cards->count(),
            'cards' => $cards,
        ]);
    }

    /**
     * 5. Kartı Puanla / Çöz (SM-2 Algoritması)
     */
    public function submitReview(Request $request)
    {
        $request->validate([
            'card_id' => 'required|exists:academic_cards,id',
            'rating' => 'required|integer|min:1|max:4', // 1=Again, 2=Hard, 3=Good, 4=Easy
        ]);

        $review = AcademicReview::firstOrCreate(
            ['card_id' => $request->card_id],
            ['repetition_count' => 0, 'ease_factor' => 2.5, 'interval_days' => 0]
        );

        $rating = (int) $request->rating;
        $rep = $review->repetition_count;
        $ease = $review->ease_factor;
        $interval = $review->interval_days;
        $now = Carbon::now();

        if ($rating === 1) { // Yeniden
            $rep = 0;
            $interval = 0;
            $nextReview = $now->copy()->addMinutes(10);
            $ease = max(1.3, $ease - 0.2);
        } elseif ($rating === 2) { // Zor
            $interval = max(1, (int) ($interval * 1.2));
            $nextReview = $now->copy()->addDays($interval);
            $ease = max(1.3, $ease - 0.15);
        } elseif ($rating === 3) { // İyi
            if ($rep === 0) {
                $interval = 1;
            } elseif ($rep === 1) {
                $interval = 6;
            } else {
                $interval = (int) ($interval * $ease);
            }
            $rep++;
            $nextReview = $now->copy()->addDays($interval);
        } else { // 4: Kolay
            if ($rep === 0) {
                $interval = 3;
            } elseif ($rep === 1) {
                $interval = 10;
            } else {
                $interval = (int) ($interval * $ease * 1.3);
            }
            $rep++;
            $ease += 0.15;
            $nextReview = $now->copy()->addDays($interval);
        }

        $review->update([
            'repetition_count' => $rep,
            'ease_factor' => $ease,
            'interval_days' => $interval,
            'next_review_at' => $nextReview,
            'last_reviewed_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'interval_days' => $interval,
            'next_review_at' => $nextReview->toDateTimeString(),
        ]);
    }

    /**
     * 6. Telefondan Amfide / Derste Tahta Fotoğrafı Yükle
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'course_name' => 'required|string',
            'topic_title' => 'required|string',
            'note_text' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        // Ders ve Konuyu Bul veya Oluştur
        $course = AcademicCourse::firstOrCreate(['name' => $request->course_name]);
        $topic = AcademicTopic::firstOrCreate([
            'course_id' => $course->id,
            'title' => $request->topic_title,
        ]);

        $imageUrl = null;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'board_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('academic_photos', $filename, 'public');
            $imageUrl = url('storage/'.$path);
        } elseif ($request->photo_base64) {
            $imageBinary = base64_decode($request->photo_base64);
            $filename = 'board_'.time().'_'.Str::random(8).'.png';
            Storage::disk('public')->put('academic_photos/'.$filename, $imageBinary);
            $imageUrl = url('storage/academic_photos/'.$filename);
        }

        // Sıradaki adımı hesapla
        $maxStep = AcademicCard::where('topic_id', $topic->id)->max('step_order') ?? 0;

        $card = AcademicCard::create([
            'topic_id' => $topic->id,
            'step_order' => $maxStep + 1,
            'front_image' => $imageUrl,
            'back_text' => $request->note_text ?? 'Tahta Notu',
            'tags_json' => $request->tags ?? ['tahta', 'amfi'],
        ]);

        // Anında ilk tekrarı başlat
        AcademicReview::create([
            'card_id' => $card->id,
            'repetition_count' => 0,
            'ease_factor' => 2.5,
            'interval_days' => 0,
            'next_review_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'card' => $card->load('topic.course', 'review'),
            'message' => 'Tahta fotoğrafı başarıyla ders notuna eklendi!',
        ]);
    }

    /**
     * 7. PC ile Çift Yönlü Toplu Senkronizasyon (Sync Delta)
     */
    public function syncBatch(Request $request)
    {
        ini_set('memory_limit', '512M');
        $payload = $request->all();
        $cards = $request->input('cards', []);
        $importedCards = 0;

        if (! empty($cards)) {
            DB::beginTransaction();
            try {
                // In-memory cache to make bulk sync blazing fast
                $coursesCache = AcademicCourse::all()->pluck('id', 'name')->toArray();
                $topicsCache = AcademicTopic::all()->mapWithKeys(function ($t) {
                    return [$t->course_id.'_'.$t->title => $t->id];
                })->toArray();

                foreach ($cards as $item) {
                    $courseName = $item['course_name'];
                    if (! isset($coursesCache[$courseName])) {
                        $course = AcademicCourse::create(['name' => $courseName]);
                        $coursesCache[$courseName] = $course->id;
                    }
                    $courseId = $coursesCache[$courseName];

                    $topicKey = $courseId.'_'.$item['topic_title'];
                    if (! isset($topicsCache[$topicKey])) {
                        $topic = AcademicTopic::create([
                            'course_id' => $courseId,
                            'title' => $item['topic_title'],
                        ]);
                        $topicsCache[$topicKey] = $topic->id;
                    }
                    $topicId = $topicsCache[$topicKey];

                    // Kart var mı?
                    $existing = AcademicCard::where('topic_id', $topicId)
                        ->where('step_order', $item['step_order'])
                        ->first();

                    if (! $existing) {
                        $card = AcademicCard::create([
                            'topic_id' => $topicId,
                            'step_order' => $item['step_order'],
                            'front_image' => $item['front_image'],
                            'back_text' => $item['back_text'],
                            'tags_json' => $item['tags'] ?? [],
                        ]);

                        AcademicReview::create([
                            'card_id' => $card->id,
                            'repetition_count' => $item['repetition_count'] ?? 0,
                            'ease_factor' => $item['ease_factor'] ?? 2.5,
                            'interval_days' => $item['interval_days'] ?? 0,
                            'next_review_at' => $item['next_review_at'] ?? Carbon::now(),
                        ]);
                        $importedCards++;
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

        // Sadece açıkça istendiğinde ve sadece telefondan URL ile yüklenen kartları geri döndür
        $since = $request->input('since_timestamp');
        $newCardsFromServer = [];
        if ($since) {
            $newCardsFromServer = AcademicCard::with(['topic.course', 'review'])
                ->where('created_at', '>', $since)
                ->where('front_image', 'like', 'http%')
                ->get();
        }

        // A. Silinmiş Dersleri ve Konuları Eşitle (Course & Topic Pruning)
        $validCourses = $request->input('valid_courses');
        if (is_array($validCourses) && count($validCourses) > 0) {
            $coursesToDelete = AcademicCourse::whereNotIn('name', $validCourses)->get();
            foreach ($coursesToDelete as $c) {
                $tIds = AcademicTopic::where('course_id', $c->id)->pluck('id');
                $cIds = AcademicCard::whereIn('topic_id', $tIds)->pluck('id');
                AcademicReview::whereIn('card_id', $cIds)->delete();
                AcademicCard::whereIn('id', $cIds)->delete();
                AcademicTopic::whereIn('id', $tIds)->delete();
                $c->delete();
            }
        }

        $validTopics = $request->input('valid_topics');
        if (is_array($validTopics)) {
            foreach ($validTopics as $cName => $topicsList) {
                $cObj = AcademicCourse::where('name', $cName)->first();
                if ($cObj && is_array($topicsList)) {
                    $topicsToDelete = AcademicTopic::where('course_id', $cObj->id)
                        ->whereNotIn('title', $topicsList)
                        ->get();
                    foreach ($topicsToDelete as $t) {
                        $cIds = AcademicCard::where('topic_id', $t->id)->pluck('id');
                        AcademicReview::whereIn('card_id', $cIds)->delete();
                        AcademicCard::whereIn('id', $cIds)->delete();
                        $t->delete();
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'received_cards_count' => count($cards),
            'imported_count' => $importedCards,
            'new_cards_from_server' => $newCardsFromServer,
            'timestamp' => Carbon::now()->toDateTimeString(),
        ]);
    }

    /**
     * 8. Deste Sil (Tekli veya Toplu)
     */
    public function deleteCourse($courseId)
    {
        $course = AcademicCourse::find($courseId);
        if ($course) {
            $tIds = AcademicTopic::where('course_id', $course->id)->pluck('id');
            $cIds = AcademicCard::whereIn('topic_id', $tIds)->pluck('id');
            AcademicReview::whereIn('card_id', $cIds)->delete();
            AcademicCard::whereIn('id', $cIds)->delete();
            AcademicTopic::whereIn('id', $tIds)->delete();
            $course->delete();
        }

        return response()->json(['success' => true]);
    }

    public function deleteCoursesBulk(Request $request)
    {
        $ids = $request->input('course_ids', []);
        if (! empty($ids)) {
            $tIds = AcademicTopic::whereIn('course_id', $ids)->pluck('id');
            $cIds = AcademicCard::whereIn('topic_id', $tIds)->pluck('id');
            AcademicReview::whereIn('card_id', $cIds)->delete();
            AcademicCard::whereIn('id', $cIds)->delete();
            AcademicTopic::whereIn('id', $tIds)->delete();
            AcademicCourse::whereIn('id', $ids)->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * 9. Konu Sil
     */
    public function deleteTopic($topicId)
    {
        $topic = AcademicTopic::find($topicId);
        if ($topic) {
            $cIds = AcademicCard::where('topic_id', $topic->id)->pluck('id');
            AcademicReview::whereIn('card_id', $cIds)->delete();
            AcademicCard::whereIn('id', $cIds)->delete();
            $topic->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * 10. Kart Sil
     */
    public function deleteCard($cardId)
    {
        AcademicReview::where('card_id', $cardId)->delete();
        AcademicCard::where('id', $cardId)->delete();

        return response()->json(['success' => true]);
    }

    /**
     * 11. Deste Yeniden Adlandır / Hiyerarşi Güncelle
     */
    public function renameCourse(Request $request, $courseId)
    {
        $request->validate(['name' => 'required|string']);
        $course = AcademicCourse::find($courseId);
        if ($course) {
            $course->name = $request->name;
            $course->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * 12. Konu Yeniden Adlandır
     */
    public function renameTopic(Request $request, $topicId)
    {
        $request->validate(['title' => 'required|string']);
        $topic = AcademicTopic::find($topicId);
        if ($topic) {
            $topic->title = $request->title;
            $topic->save();
        }

        return response()->json(['success' => true]);
    }
}
