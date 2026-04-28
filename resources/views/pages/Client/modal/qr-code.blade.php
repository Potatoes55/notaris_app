{{-- 
    Modal didorong ke stack 'modal_luar' agar berada di luar pembungkus .main-content.
    Ini solusi mutlak agar tidak tertutup sidebar atau backdrop yang salah layer.
--}}
@push('modal_luar')
<div class="modal fade" id="qrModal{{ $client->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4 text-center border-radius-xl shadow-lg border-0 bg-white">
            {{-- Header Modal --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 font-weight-bolder">QR Code Klien</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="font-weight-bold">&times;</span>
                </button>
            </div>

            @php
                $link = url("/clients/{$client->uuid}");
                $dns2d = new \Milon\Barcode\DNS2D();
                // Ukuran 10,10 sudah pas untuk resolusi tinggi
                $pngData = $dns2d->getBarcodePNG($link, 'QRCODE', 10, 10);
                $qrCodePng = base64_encode($pngData);
            @endphp

            {{-- Container QR: Putih Bersih --}}
            <div class="bg-white p-3 border-radius-lg shadow-sm d-inline-block mx-auto mb-4 border">
                <img src="data:image/png;base64,{{ $qrCodePng }}" alt="QR Code" style="width: 220px; height: 220px;" />
            </div>

            {{-- Info Link & Copy --}}
            <div class="mb-4">
                <p class="text-xs text-uppercase font-weight-bold text-muted mb-2 text-center">Link Akses Form Klien</p>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control bg-light border-0 ps-3" value="{{ $link }}" id="linkInput{{ $client->id }}" readonly>
                    <button class="btn btn-primary mb-0" type="button" onclick="copyLink('{{ $link }}')">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row g-2">
                <div class="col-6">
                    <a href="data:image/png;base64,{{ $qrCodePng }}" download="qrcode-{{ $client->uuid }}.png"
                        class="btn btn-dark w-100 mb-0">
                        <i class="fas fa-download me-2"></i> Download
                    </a>
                </div>
                <div class="col-6">
                    <a href="https://wa.me/?text={{ urlencode('Silakan akses link pendaftaran klien Notaris: ' . $link) }}" 
                        target="_blank"
                        class="btn btn-success w-100 mb-0 text-white">
                        <i class="fab fa-whatsapp me-2"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@once
    @push('js')
    <script>
        function copyLink(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Opsional: Ganti alert dengan toast agar lebih modern
                alert('Link berhasil disalin!');
            }).catch(err => {
                console.error('Gagal menyalin text: ', err);
            });
        }
    </script>
    @endpush
@endonce