@extends('layouts.app') {{-- Pastikan layout ini ada, atau ganti ke layout yang kamu pakai --}}

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja</h2>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">Lanjut Belanja</a>
    </div>

    @if($cartItems->count() > 0)
    <form action="{{ route('checkout.selected') }}" method="POST">
        @csrf
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 50px;">Pilih</th>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th style="width: 150px;">Jumlah</th>
                            <th>Subtotal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $item)
                        <tr>
                            <td class="ps-4">
                                <input type="checkbox" name="cart_ids[]" value="{{ $item->id }}" class="form-check-input cart-checkbox" data-price="{{ $item->product->price * $item->quantity }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . $item->product->image) }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $item->product->name }}</h6>
                                        <small class="text-muted">{{ $item->product->brand }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>IDR {{ number_format($item->product->price) }}</td>
                            <td>
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" onchange="this.form.submit()" class="form-control form-control-sm">
                                </form>
                            </td>
                            <td class="fw-bold">IDR {{ number_format($item->product->price * $item->quantity) }}</td>
                            <td class="text-center">
                                <form action="{{ route('cart.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus barang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm border-0 mt-4 p-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">* Centang produk yang ingin kamu bayar sekarang.</p>
                </div>
                <div class="col-md-6 text-end">
                    <h5 class="mb-3 text-muted">Total Pilihan: <span class="text-danger fw-bold" id="selected-total">IDR 0</span></h5>
                    <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold shadow">
                        Checkout Terpilih
                    </button>
                </div>
            </div>
        </div>
    </form>
    @else
    <div class="text-center py-5 bg-white rounded shadow-sm">
        <i class="fas fa-shopping-basket fa-4x text-light mb-3"></i>
        <h4>Keranjangmu masih kosong</h4>
        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Cari Sepatu Sekarang</a>
    </div>
    @endif
</div>

{{-- SCRIPT SEDERHANA BUAT HITUNG TOTAL OTOMATIS SAAT DICENTANG --}}
<script>
    document.querySelectorAll('.cart-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            let total = 0;
            document.querySelectorAll('.cart-checkbox:checked').forEach(checked => {
                total += parseInt(checked.getAttribute('data-price'));
            });
            document.getElementById('selected-total').innerText = 'IDR ' + new Intl.NumberFormat('id-ID').format(total);
        });
    });
</script>
@endsection
