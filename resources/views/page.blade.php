<x-frontend-layout 
    :title="$page->title . ' - İstanbul Su Arıtma Marketi'" 
    :description="Str::limit(strip_tags($page->content), 155)">
    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 p-8">
                <h1 class="text-3xl font-extrabold text-gray-900 mb-8 border-b pb-4">{{ $page->title }}</h1>
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($page->content)) !!}
                </div>
            </div>
        </div>
    </div>
</x-frontend-layout>
