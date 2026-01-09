<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan | SHOESPRO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .order-card { border: none; border-radius: 15px; transition: 0.3s; }
        .order-card:hover { box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .status-badge { border-radius: 50px; padding: 5px 15px; font-size: 0.8rem; font-weight: 600; }
        .product-img { width: 70px; height: 70px; object-fit: cover; border-radius: 10px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="fas fa-box text-primary me-2"></i>Riwayat Pesanan</h3>
        <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm rounded-pill px-4">
            <i class="fas fa-arrow-left me-1"></i> Kembali Belanja
        </a>
    </div>

    @forelse($orders as $order)
    <div class="card order-card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <div>
                <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">ID Pesanan</small>
                <span class="fw-bold text-dark">{{ $order->order_number }}</span>
                <span class="text-muted mx-2">•</span>
                <small class="text-muted">{{ $order->created_at->format('d M Y') }}</small>
            </div>
            <div>
                @if($order->status == 'processing')
                    <span class="status-badge bg-warning text-dark"><i class="fas fa-spinner fa-spin me-1"></i> DIPROSES</span>
                @elseif($order->status == 'completed')
                    <span class="status-badge bg-success text-white"><i class="fas fa-check-circle me-1"></i> SELESAI</span>
                @else
                    <span class="status-badge bg-light text-dark">{{ strtoupper($order->status) }}</span>
                @endif
            </div>
        </div>

        <div class="card-body">
            @foreach($order->items as $item)
            <div class="d-flex align-items-center mb-3">
                <img src="{{ asset('storage/' . $item->product->image) }}" class="product-img border me-3" alt="produk">
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold">{{ $item->product->name }}</h6>
                    <small class="text-muted">{{ $item->quantity }} Pasang x IDR {{ number_format($item->price, 0, ',', '.') }}</small>
                </div>
                <div class="text-end">
                    <span class="fw-bold text-dark">IDR {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach

            <hr class="my-3 opacity-25">

            <div class="row align-items-end">
                <div class="col-md-8">
                    <small class="text-muted d-block mb-1"><i class="fas fa-map-marker-alt me-1"></i> Alamat Pengiriman:</small>
                    <p class="small mb-0 text-dark">{{ $order->address }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <small class="text-muted d-block mb-1">Total Pembayaran</small>
                    <h4 class="fw-bold text-danger mb-0">IDR {{ number_format($order->total_price, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5 shadow-sm bg-white rounded-4 mt-5">
        <img src="https://cdn-icons-png.flaticon.com/512/11698/11698625.png" style="width: 150px; opacity: 0.6;" alt="empty">
        <h4 class="mt-4 fw-bold">Belum ada pesanan, bro!</h4>
        <p class="text-muted">Yuk, cari sepatu impianmu sekarang sebelum kehabisan.</p>
        <a href="{{ route('home') }}" class="btn btn-primary px-5 rounded-pill shadow-sm mt-2">Mulai Belanja</a>
    </div>
    @endforelse
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
