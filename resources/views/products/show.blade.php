@extends('layouts.frontend')

@section('title', $product->name)

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Products</a></li>
            @if ($product->category)
                <li class="breadcrumb-item"><a href="{{ route('category.show', $product->category->slug) }}" class="text-decoration-none">{{ $product->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($product->name, 40) }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Product Images -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <img src="{{ $product->image_url }}" class="img-fluid rounded product-image w-100" alt="{{ $product->name }}">
                </div>
            </div>

            @if ($product->images->count() > 1)
                <div class="row g-2 mt-3">
                    @foreach ($product->images as $image)
                        <div class="col-3">
                            <img src="{{ $image->image_url }}" class="img-fluid rounded" alt="{{ $product->name }}" style="height: 80px; object-fit: cover; width: 100%; cursor: pointer;" onclick="changeMainImage('{{ $image->image_url }}')">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="mb-3">
                <h1 class="h2 fw-bold mb-2">{{ $product->name }}</h1>

                <div class="d-flex flex-wrap gap-2 mb-3">
                    @if ($product->featured)
                        <span class="badge bg-danger">Featured</span>
                    @endif
                    @if ($product->category)
                        <a href="{{ route('category.show', $product->category->slug) }}" class="badge bg-info text-white text-decoration-none">{{ $product->category->name }}</a>
                    @endif
                </div>
            </div>

            <!-- Rating Display -->
            @if ($product->rating_count > 0)
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= round($product->average_rating) ? '-fill' : '' }} text-warning"></i>
                        @endfor
                    </div>
                    <div>
                        <strong class="text-dark">{{ number_format($product->average_rating, 1) }}/5</strong>
                        <small class="text-muted">({{ $product->rating_count }} reviews)</small>
                    </div>
                </div>
            @endif

            <!-- Price & Stock Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Price</div>
                            <div class="h4 mb-0 text-primary fw-bold">${{ number_format($product->selling_price, 2) }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Availability</div>
                            <span class="badge fs-6 {{ $product->stock <= $product->alert_stock ? 'bg-danger' : 'bg-success' }}">
                                {{ $product->stock }} {{ $product->unit->short_name ?? $product->unit->name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if ($product->description)
                <div class="mb-4">
                    <h5 class="fw-bold mb-2">Description</h5>
                    <p class="text-muted mb-0">{{ $product->description }}</p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="d-flex gap-2 mb-4">
                <form method="POST" action="{{ route('cart.add', $product->id) }}" class="flex-grow-1">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-cart-plus me-2"></i>Add to Cart
                    </button>
                </form>
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-arrow-left me-2"></i>Back to Products
                </a>
            </div>

            <!-- SKU -->
            <div class="text-muted small">
                <strong>SKU:</strong> <code>{{ $product->sku }}</code>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-star-half text-warning me-2"></i>
                            Customer Reviews
                            @if ($product->rating_count > 0)
                                <span class="badge bg-primary ms-2">{{ $product->rating_count }}</span>
                            @endif
                        </h4>
                        @if ($product->rating_count > 0)
                            <div class="text-end">
                                <div class="h5 mb-0 text-warning">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= round($product->average_rating) ? '-fill' : '' }}"></i>
                                    @endfor
                                    <span class="ms-2 text-dark">{{ number_format($product->average_rating, 1) }}/5</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if ($product->approvedReviews->count() > 0)
                        @foreach ($product->approvedReviews as $review)
                            <div class="review-card p-3 mb-3 rounded">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong class="text-dark">{{ $review->user->name }}</strong>
                                        <div class="review-rating mt-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                            @endfor
                                            <small class="text-muted ms-2">{{ $review->rating }}/5</small>
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                </div>
                                @if ($review->comment)
                                    <p class="mb-0 text-dark">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-chat-square-quote display-4 text-muted mb-3"></i>
                            <p class="text-muted mb-0">No reviews yet. Be the first to review this product!</p>
                        </div>
                    @endif

                    @if ($userCanReview)
                        <div class="mt-4 p-4 bg-light rounded">
                            <h5 class="mb-3">{{ $userReview ? 'Edit Your Review' : 'Write a Review' }}</h5>
                            <form method="POST" action="{{ route('products.review.store', $product) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Your Rating <span class="text-danger">*</span></label>
                                    <div class="rating-stars mb-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ ($userReview && $userReview->rating == $i) || (!$userReview && $i == 5) ? 'checked' : '' }} required>
                                            <label for="star{{ $i }}" title="{{ $i }} star{{ $i > 1 ? 's' : '' }}">
                                                <i class="bi bi-star{{ $i <= ($userReview ? $userReview->rating : 0) ? '-fill' : '' }}"></i>
                                            </label>
                                        @endfor
                                    </div>
                                    <small class="text-muted">Click to rate this product</small>
                                </div>
                                <div class="mb-3">
                                    <label for="comment" class="form-label fw-semibold">Your Review (optional)</label>
                                    <textarea class="form-control" id="comment" name="comment" rows="4" maxlength="1000" placeholder="Share your thoughts about this product...">{{ $userReview ? $userReview->comment : '' }}</textarea>
                                    <div class="form-text">Maximum 1000 characters</div>
                                </div>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-send me-2"></i>{{ $userReview ? 'Update Review' : 'Submit Review' }}
                                </button>
                            </form>
                        </div>
                    @elseif (Auth::check())
                        <div class="mt-4 alert alert-info border-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Purchase Required:</strong> You need to purchase this product to leave a review.
                        </div>
                    @else
                        <div class="mt-4 alert alert-warning border-0">
                            <i class="bi bi-person-circle me-2"></i>
                            <strong>Login Required:</strong> <a href="{{ route('login') }}" class="alert-link">Login</a> to write a review.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function changeMainImage(src) {
    document.querySelector('.product-image').src = src;
}
</script>
@endsection
