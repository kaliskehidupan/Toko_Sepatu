<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | SHOESPRO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="CLIENT_KEY_ANDA"></script>
    <style>
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3 class="fw-bold mb-4">Konfirmasi Pesanan</h3>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Daftar Barang</h5>
                    @foreach($items as $item)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('storage/' . $item->product->image) }}" class="product-img me-3" alt="{{ $item->product->name }}">
                            <div>
                                <h6 class="fw-bold mb-0">{{ $item->product->name }}</h6>
                                <small class="text-muted">Jumlah: {{ $item->quantity }}x</small>
                            </div>
                        </div>
                        <span class="fw-bold">IDR {{ number_format($item->product->price * $item->quantity) }}</span>
                    </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5 class="fw-bold">Total Bayar</h5>
                        <h5 class="fw-bold text-danger">IDR {{ number_format($total) }}</h5>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Alamat Pengiriman</h5>
                    <textarea id="address" class="form-control" rows="3" placeholder="Masukkan alamat lengkap pengiriman..." required></textarea>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="button" id="pay-button" class="btn btn-primary btn-lg fw-bold">Bayar Sekarang</button>
                <a href="{{ url()->previous() }}" class="btn btn-light">Batal</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#pay-button').click(function (e) {
        e.preventDefault();

        let address = $('#address').val();
        if (!address) {
            alert('Alamat pengiriman wajib diisi!');
            return;
        }

        let btn = $(this);
        btn.html('<span class="spinner-border spinner-border-sm"></span> Memproses...').prop('disabled', true);

        // 1. Simpan pesanan ke database
        $.post("{{ route('order.process') }}", {
            _token: "{{ csrf_token() }}",
            address: address,
            products: JSON.stringify([
                @foreach($items as $item)
                {
                    "product_id": {{ $item->product->id }},
                    "quantity": {{ $item->quantity }}
                },
                @endforeach
            ])
        })
        .done(function(orderResponse) {
            // 2. Ambil Snap Token dari Midtrans
            $.post("{{ route('payment.token') }}", {
                _token: "{{ csrf_token() }}",
                total_price: "{{ $total }}",
                order_id: orderResponse.order_number
            })
            .done(function(midtransData) {
                // 3. Popup Midtrans
                window.snap.pay(midtransData.snap_token, {
                    onSuccess: function(result) {
                        window.location.href = "{{ route('cart.clear') }}";
                    },
                    onPending: function(result) {
                        window.location.href = "{{ route('orders.index') }}";
                    },
                    onError: function(result) {
                        alert("Pembayaran Gagal!");
                        location.reload();
                    }
                });
            })
            .fail(function() {
                alert('Gagal mengambil token pembayaran. Cek Server Key Midtrans kamu.');
                btn.html('Bayar Sekarang').prop('disabled', false);
            });
        })
        .fail(function(xhr) {
            alert('Gagal simpan pesanan: ' + xhr.responseText);
            btn.html('Bayar Sekarang').prop('disabled', false);
        });
    });
</script>
</body>
</html>
