<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shoes Store | Koleksi Sepatu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-card { border: none; border-radius: 12px; transition: 0.3s; background: #fff; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .category-sidebar { background: white; border-radius: 12px; padding: 20px; position: sticky; top: 80px; }
        .navbar { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .img-wrapper { background: #f8f9fa; border-radius: 10px; overflow: hidden; height: 200px; display: flex; align-items: center; justify-content: center; }
        .img-wrapper img { max-height: 100%; object-fit: contain; }
        .dropdown-menu { z-index: 1050 !important; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">👟 SHOES<span class="text-primary">PRO</span></a>

        <div class="collapse navbar-collapse" id="navbarNav">
            <form action="{{ route('home') }}" method="GET" class="d-flex mx-auto w-50">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari merk atau nama sepatu..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>

            <ul class="navbar-nav ms-auto align-items-center">
                @auth
                <li class="nav-item me-3">
                    <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                        <i class="fas fa-shopping-cart fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ \App\Models\Cart::where('user_id', auth()->id())->count() }}
                        </span>
                    </a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link fw-bold" href="{{ route('orders.index') }}"><i class="fas fa-box me-1"></i> Pesanan</a>
                </li>
                @endauth

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-bold text-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> Hai, {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-id-card me-2"></i> Profil Saya</a></li>
                            @if(auth()->user()->role == 'admin')
                                <li><a class="dropdown-item text-primary fw-bold" href="/admin"><i class="fas fa-user-shield me-2"></i> Panel Admin</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger w-100 text-start border-0 bg-transparent">
                                        <i class="fas fa-sign-out-alt me-2"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm px-4 shadow-sm" href="{{ route('login') }}">Login</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-3">
            <div class="category-sidebar shadow-sm">
                <h6 class="fw-bold mb-3"><i class="fas fa-filter me-2"></i>Filter Produk</h6>
                <form action="{{ route('home') }}" method="GET">
                    <input type="hidden" name="search" value="{{ request('search') }}">

                    <div class="mb-3">
                        <label class="small text-muted fw-bold text-uppercase">Kategori</label>
                        <select name="category" class="form-select form-select-sm mt-1" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="small text-muted fw-bold text-uppercase">Brand / Merk</label>
                        <div class="mt-2">
                            @foreach(['Nike', 'Adidas', 'Puma', 'Vans', 'Ortuseight', 'Mills'] as $brand)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="brand" value="{{ $brand }}"
                                        id="brand{{ $brand }}" onchange="this.form.submit()"
                                        {{ request('brand') == $brand ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="brand{{ $brand }}">{{ $brand }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('home') }}" class="btn btn-sm btn-light w-100 border text-muted">Reset Semua</a>
                </form>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="row g-4">
                @forelse($products as $product)
                <div class="col-md-4">
                    <div class="card h-100 product-card shadow-sm p-3 border-0 text-center">
                        <div class="img-wrapper mb-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#detailModal{{ $product->id }}">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                        </div>

                        <div class="card-body p-0">
                            <span class="badge bg-light text-primary mb-2 border">{{ $product->brand ?? 'Original' }}</span>
                            <h6 class="fw-bold mb-1" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#detailModal{{ $product->id }}">
                                {{ $product->name }}
                            </h6>
                            <p class="text-muted small mb-2">{{ $product->category->name ?? 'Uncategorized' }}</p>
                            <h5 class="text-danger fw-bold">IDR {{ number_format($product->price) }}</h5>
                        </div>

                        <div class="card-footer bg-transparent border-0 p-0 mt-3">
                        @auth
                            <div class="d-flex gap-2">
                                {{-- Tombol Beli Langsung --}}
                                <a href="{{ route('buy.now', $product->id) }}" class="btn btn-danger flex-grow-1 py-2 fw-bold">
                                    Beli
                                </a>
                                {{-- Tombol Tambah Keranjang --}}
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-dark py-2 px-3">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-danger w-100 py-2 fw-bold text-decoration-none">
                                <i class="fas fa-sign-in-alt me-2"></i> Login untuk Beli
                            </a>
                        @endauth
                    </div>
                    </div>
                </div>

                <div class="modal fade" id="detailModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-0 pb-0">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center pt-0">
                                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded mb-3" style="max-height: 250px;">
                                <div class="text-start px-3">
                                    <h4 class="fw-bold mb-1">{{ $product->name }}</h4>
                                    <span class="badge bg-primary mb-3">{{ $product->brand }}</span>

                                    <h6 class="fw-bold">Deskripsi:</h6>
                                    <p class="text-muted small">{{ $product->description }}</p>

                                    <div class="bg-light p-3 rounded d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <small class="text-muted d-block">Harga</small>
                                            <h4 class="text-danger fw-bold mb-0">IDR {{ number_format($product->price) }}</h4>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block">Stok</small>
                                            <span class="fw-bold text-dark">{{ $product->stock }} Pasang</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-3">
                                @auth
                                    <div class="d-flex w-100 gap-2">
                                        <a href="{{ route('buy.now', $product->id) }}" class="btn btn-danger flex-grow-1 fw-bold py-2">
                                            Beli Sekarang
                                        </a>
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-dark py-2 px-3">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary w-100 py-2">Login untuk Checkout</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Tidak ada produk ditemukan.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
