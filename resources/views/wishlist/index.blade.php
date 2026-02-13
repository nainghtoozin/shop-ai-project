@extends('layouts.frontend')

@section('title', 'My Wishlist')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">My Wishlist</h1>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($wishlists->count() > 0)
                <div class="row g-4">
                    @foreach ($wishlists as $wishlist)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="position-relative">
                                    <img src="{{ $wishlist->product->image_url }}" class="card-img-top" alt="{{ $wishlist->product->name }}" style="height: 200px; object-fit: cover;">
                                    <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                            onclick="removeFromWishlist({{ $wishlist->product->id }})">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">{{ $wishlist->product->name }}</h6>
                                    <p class="card-text text-primary fw-bold">${{ number_format($wishlist->product->selling_price, 2) }}</p>
                                    <div class="mt-auto">
                                        <form method="POST" action="{{ route('wishlist.move-to-cart', $wishlist->product) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm me-2">
                                                <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                            </button>
                                        </form>
                                        <a href="{{ route('products.show', $wishlist->product->slug) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-heart display-4 text-muted mb-3"></i>
                    <h4 class="text-muted">Your wishlist is empty</h4>
                    <p class="text-muted">Add products you love to your wishlist!</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Browse Products</a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function removeFromWishlist(productId) {
    if (confirm('Remove this product from your wishlist?')) {
        fetch(`/wishlist/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}
</script>
@endsection