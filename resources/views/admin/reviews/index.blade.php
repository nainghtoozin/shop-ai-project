@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Reviews</h1>
            <p class="text-muted mb-0">Manage product reviews</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Reviews Table -->
        <div class="card shadow">
            <div class="card-body p-0">
                @if ($reviews->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">User</th>
                                    <th scope="col">Rating</th>
                                    <th scope="col">Comment</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reviews as $review)
                                    <tr>
                                        <td>{{ $review->product->name ?? 'N/A' }}</td>
                                        <td>
                                            <div>{{ $review->user->name }}</div>
                                            <small class="text-muted">{{ $review->user->email }}</small>
                                        </td>
                                        <td>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }} text-warning"></i>
                                            @endfor
                                        </td>
                                        <td>
                                            @if ($review->comment)
                                                {{ Str::limit($review->comment, 50) }}
                                            @else
                                                <em>No comment</em>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($review->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif ($review->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ $review->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if ($review->status === 'pending')
                                                <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="bi bi-check-circle"></i> Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-warning btn-sm">
                                                        <i class="bi bi-x-circle"></i> Reject
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-star-half display-4 text-muted mb-3"></i>
                        <h5 class="text-muted">No reviews found</h5>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if ($reviews->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
@endsection