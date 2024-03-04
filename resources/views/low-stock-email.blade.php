<h1>De volgende producten zijn bijna op:</h1>

<ul>
    @foreach ($products as $product)
        <b>{{$product->name}}</b> - {{$product->stock}} / {{$product->initialStock}}
    @endforeach
</ul>
