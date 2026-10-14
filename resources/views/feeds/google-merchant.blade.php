<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <channel>
        <title>{{ $settings['site_name'] ?? 'İstanbul Su Arıtma Marketi' }}</title>
        <link>{{ url('/') }}</link>
        <description>{{ $settings['seo_description'] ?? 'İstanbul Su Arıtma Cihazları, Orijinal Filtreler ve Servis Hizmetleri' }}</description>
        
        @foreach($products as $product)
            @php
                $cleanDesc = trim(strip_tags($product->short_description ?: $product->description ?: $product->name));
                $cleanDesc = mb_substr($cleanDesc, 0, 4900);
                $mainImg = $product->image_path ? asset('storage/' . $product->image_path) : url('/images/placeholder.png');
            @endphp
            <item>
                <g:id>{{ $product->id }}</g:id>
                <g:title><![CDATA[{{ $product->name }}]]></g:title>
                <g:description><![CDATA[{{ $cleanDesc }}]]></g:description>
                <g:link>{{ route('product.show', $product->slug) }}</g:link>
                <g:image_link>{{ $mainImg }}</g:image_link>
                
                @foreach($product->images as $extraImg)
                    <g:additional_image_link>{{ asset('storage/' . $extraImg->image_path) }}</g:additional_image_link>
                @endforeach

                <g:availability>{{ $product->in_stock ? 'in_stock' : 'out_of_stock' }}</g:availability>
                <g:price>{{ number_format($product->price, 2, '.', '') }} TRY</g:price>
                
                @if(!empty($product->campaign_price) && $product->campaign_price < $product->price)
                    <g:sale_price>{{ number_format($product->campaign_price, 2, '.', '') }} TRY</g:sale_price>
                @endif

                <g:brand><![CDATA[{{ $settings['site_name'] ?? 'İstanbul Su Arıtma Marketi' }}]]></g:brand>
                <g:condition>new</g:condition>
                <g:google_product_category>680</g:google_product_category>
                <g:product_type><![CDATA[{{ $product->category->name ?? 'Su Arıtma Cihazları' }}]]></g:product_type>
                <g:identifier_exists>no</g:identifier_exists>

                <g:shipping>
                    <g:country>TR</g:country>
                    <g:service>Standart Teslimat &amp; Randevulu Ücretsiz Montaj</g:service>
                    <g:price>0.00 TRY</g:price>
                </g:shipping>
            </item>
        @endforeach
    </channel>
</rss>
