@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Services Management Module</h3>
                    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Service
                    </a>
                </div>

                <class class="card-body">
                    <!-- Search & Filter -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control"
                                       placeholder="Search by title/product name..."
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>
                                        All Status
                                    </option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active/In Stock</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive/Out of Stock</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                            <div class="col-md-3">
                                @if(request('search') || request('status') !== 'all')
                                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary w-100">
                                        Clear Filters
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <!-- Services Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Title/Product Name</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                <tr>
                                    <td>
                                        @if($service->image)
                                            <img src="{{ Storage::url($service->image) }}"
                                                 alt="{{ $service->title_or_product_name }}"
                                                 class="rounded"
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded p-3 d-flex align-items-center justify-content-center"
                                                 style="width: 60px; height: 60px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $service->title_or_product_name }}</td>
                                    <td>₹{{ number_format($service->price ?? 0, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $service->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $service->status ? 'Active/In Stock' : 'Inactive/Out of Stock' }}
                                        </span>
                                    </td>
                                    <td>{{ $service->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.services.edit', $service) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.services.destroy', $service) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this service?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No services found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!--double!! Pagination -->
                     {{ $services->appends(request()->query())->links()}}
                </class>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
