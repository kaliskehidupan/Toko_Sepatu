<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja | SHOESPRO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        body { background: #f8f9fa; }
        .table img { width: 60px; border-radius: 8px; }
        .card { border: none; border-radius: 15px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="fas fa-shopping-cart me-2 text-primary"></i>Keranjang Belanja</h2>
            <a href="{{ route('home') }}" class="btn btn-outline-dark rounded-pill">
                <i class="fas fa-arrow-left me-1"></i> Kembali Belanja
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Produk</th>
                                <th>Harga</th>
                                <th style="width: 130px;">Jumlah</th>
                                <th>Subtotal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cartItems as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/' . $item->product->image) }}" class="me-3 border">
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $item->product->name }}</h6>
                                            <small class="text-muted">{{ $item->product->brand }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>IDR {{ number_format($item->product->price) }}</td>
                                <td>
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                        @csrf
                                        <input type="number" name="quantity" value="{{ $item->quantity }}"
                                               min="1" class="form-control form-control-sm text-center" onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td class="fw-bold">IDR {{ number_format($item->product->price * $item->quantity) }}</td>
                                <td class="text-center">
                                    {{-- FIX: Menggunakan form DELETE agar sinkron dengan route cart.destroy --}}
                                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <p class="text-muted mb-0">Keranjang kamu masih kosong nih.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white border-top-0 p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">Total Pembayaran:</h5>
                        <h3 class="text-danger fw-bold mb-0">IDR {{ number_format($total) }}</h3>
                    </div>
                    <div class="col-md-6 text-end">
                        @auth
                            @if($total > 0)
                                <button class="btn btn-primary btn-lg px-5 fw-bold shadow" id="pay-button">
                                    <i class="fas fa-credit-card me-2"></i>Bayar Sekarang
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 fw-bold shadow">
                                Login untuk Checkout
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('#pay-button').click(function (event) {
            event.preventDefault();
            let btn = $(this);

            // 1. Minta Alamat (karena database butuh field address)
            let alamat = prompt("Konfirmasi Alamat Pengiriman:", "Jl. Sepatu No. 123, Jakarta");

            if (!alamat) {
                alert("Alamat wajib diisi untuk memproses pesanan!");
                return;
            }

            btn.html('<i class="fas fa-spinner fa-spin"></i> Menyiapkan Pesanan...').prop('disabled', true);

            // 2. LANGKAH PERTAMA: Simpan pesanan ke Database Laravel
            $.post("{{ route('order.process') }}", {
                _token: "{{ csrf_token() }}",
                address: alamat,
                // Mengirim data produk dari blade ke JSON
                products: JSON.stringify([
                    @foreach($cartItems as $item)
                    {
                        "product_id": {{ $item->product_id }},
                        "quantity": {{ $item->quantity }},
                        "cart_id": {{ $item->id }}
                    },
                    @endforeach
                ])
            })
            .done(function(orderResponse) {
                // 3. LANGKAH KEDUA: Ambil Snap Token dari Midtrans
                btn.html('<i class="fas fa-spinner fa-spin"></i> Menghubungkan ke Bank...');

                $.post("{{ route('payment.token') }}", {
                    _token: "{{ csrf_token() }}",
                    total_price: "{{ $total }}",
                    order_id: orderResponse.order_number // Kirim nomor order agar Midtrans sinkron
                })
                .done(function(midtransData) {
                    // 4. LANGKAH KETIGA: Munculkan Popup Midtrans
                    window.snap.pay(midtransData.snap_token, {
                        onSuccess: function(result) {
                            alert("Mantap! Pembayaran Berhasil.");
                            window.location.href = "{{ route('cart.clear') }}";
                        },
                        onPending: function(result) {
                            alert("Pesanan kamu sudah tercatat. Segera bayar ya!");
                            window.location.href = "{{ route('orders.index') }}";
                        },
                        onError: function(result) {
                            alert("Duh, pembayaran gagal. Coba lagi yuk.");
                            location.reload();
                        }
                    });
                })
                .fail(function() {
                    alert('Gagal mengambil token Midtrans. Cek file .env (Client Key).');
                });
            })
            .fail(function() {
                alert('Gagal simpan pesanan ke database. Pastikan tabel orders sudah ada.');
            })
            .always(function() {
                btn.html('<i class="fas fa-credit-card me-2"></i>Bayar Sekarang').prop('disabled', false);
            });
        });
    </script>
</body>
</html>
