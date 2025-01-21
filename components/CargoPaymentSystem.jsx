/** 
 * Fixed Cargo Payment System Component
 */
const { useState, useEffect } = React;

function CargoPaymentSystem() {
  const [paymentMethod, setPaymentMethod] = useState('');
  const [cargoDetails, setCargoDetails] = useState(null);
  const [totalPrice, setTotalPrice] = useState(0);
  const [virtualAccount, setVirtualAccount] = useState('');
  const [countdown, setCountdown] = useState(3600); // 1 hour in seconds
  const [copied, setCopied] = useState(false);
  const [error, setError] = useState(null);

  useEffect(() => {
    try {
      // Get cargo booking data and total price from sessionStorage
      const cargoBookingData = JSON.parse(sessionStorage.getItem('cargoBookingData'));
      const totalPriceStr = sessionStorage.getItem('totalPrice');
      
      console.log('Retrieved data:', {
        cargoBookingData,
        totalPrice: totalPriceStr
      });
      
      if (cargoBookingData && totalPriceStr) {
        setCargoDetails(cargoBookingData);
        setTotalPrice(parseInt(totalPriceStr));
      }

      // Generate virtual account number
      const va = '88' + Math.random().toString().slice(2, 14);
      setVirtualAccount(va);

      // Set countdown timer
      const timer = setInterval(() => {
        setCountdown(prev => {
          if (prev <= 0) {
            clearInterval(timer);
            return 0;
          }
          return prev - 1;
        });
      }, 1000);

      return () => clearInterval(timer);
    } catch (err) {
      console.error('Error in useEffect:', err);
      setError('Terjadi kesalahan saat memuat data');
    }
  }, []);

  const formatTime = (seconds) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
  };

  const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  const handlePayment = async () => {
    try {
      setError(null);

      // Check session first
      const sessionResponse = await fetch('check_session.php');
      const sessionText = await sessionResponse.text();
      console.log('Session response:', sessionText);

      let sessionData;
      try {
        sessionData = JSON.parse(sessionText);
      } catch (e) {
        throw new Error('Sesi tidak valid, silakan login ulang');
      }

      if (!sessionData.user_id) {
        alert('Silakan login terlebih dahulu');
        window.location.href = 'login.html';
        return;
      }

      // Get cargo booking data
      const cargoBookingData = JSON.parse(sessionStorage.getItem('cargoBookingData'));
      if (!cargoBookingData) {
        throw new Error('Data cargo tidak ditemukan');
      }

      // Format date properly
      const tanggal = new Date(cargoBookingData.cargoDetails.tanggalPengiriman);
      const formattedDate = tanggal.toISOString().split('T')[0];

      // Prepare cargo data matching database structure
      const cargoData = {
        user_id: sessionData.user_id,
        asal: cargoBookingData.pengirim.kota,
        tujuan: cargoBookingData.penerima.kota,
        jenis: cargoBookingData.cargoDetails.jenisBarang,
        berat_kg: parseFloat(cargoBookingData.cargoDetails.beratBarang),
        tanggal: cargoBookingData.cargoDetails.tanggalPengiriman,
        nama_pengirim: cargoBookingData.pengirim.nama,
        alamat_pengirim: cargoBookingData.pengirim.alamat,
        kota_pengirim: cargoBookingData.pengirim.kota,
        kodepos_pengirim: cargoBookingData.pengirim.kodePos,
        telepon_pengirim: cargoBookingData.pengirim.telepon,
        nama_penerima: cargoBookingData.penerima.nama,
        alamat_penerima: cargoBookingData.penerima.alamat,
        kota_penerima: cargoBookingData.penerima.kota,
        kodepos_penerima: cargoBookingData.penerima.kodePos,
        telepon_penerima: cargoBookingData.penerima.telepon,
        catatan: cargoBookingData.catatan || '',
        status: 'aktif'
    };

      console.log('Sending cargo data:', cargoData);

      // Save cargo data
      const saveResponse = await fetch('save_cargo.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(cargoData)
      });

      const saveText = await saveResponse.text();
      console.log('Save response:', saveText);

      let result;
      try {
        result = JSON.parse(saveText);
      } catch (e) {
        console.error('Error parsing save response:', e, saveText);
        throw new Error('Gagal memproses respon server');
      }

      if (result.success) {
        // Clear session storage before redirecting
        sessionStorage.removeItem('cargoBookingData');
        sessionStorage.removeItem('totalPrice');
        window.location.href = 'payment_success.html';
      } else {
        throw new Error(result.error || 'Gagal menyimpan data cargo');
      }
    } catch (error) {
      console.error('Payment error:', error);
      setError(error.message);
      alert('Terjadi kesalahan: ' + error.message);
    }
  };

  return (
    <div className="w-full max-w-3xl mx-auto p-6">
      <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 className="text-2xl font-bold mb-4">Detail Pembayaran Cargo</h2>
        
        {error && (
          <div className="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-4">
            {error}
          </div>
        )}
        
        {cargoDetails && (
          <div className="mb-6">
            <p className="text-gray-600">
              Rute: {cargoDetails.pengirim.kota} → {cargoDetails.penerima.kota}
            </p>
            <p className="text-gray-600">
              Jenis Barang: {cargoDetails.cargoDetails.jenisBarang}
            </p>
            <p className="text-gray-600">
              Berat: {cargoDetails.cargoDetails.beratBarang} Kg
            </p>
            <p className="text-xl font-semibold mt-2">
              Total: Rp {totalPrice?.toLocaleString()}
            </p>
          </div>
        )}

        <div className="mb-6">
          <h3 className="text-lg font-semibold mb-2">Waktu Pembayaran Tersisa</h3>
          <div className="text-2xl font-mono bg-gray-100 p-4 rounded text-center">
            {formatTime(countdown)}
          </div>
        </div>

        <div className="space-y-4">
          {/* Bank Transfer Option */}
          <div
            className="border rounded-lg p-4 cursor-pointer hover:bg-gray-50"
            onClick={() => setPaymentMethod('bank')}
          >
            <h3 className="font-semibold">Transfer Bank</h3>
            {paymentMethod === 'bank' && (
              <div className="mt-4 space-y-4">
                <div className="bg-gray-50 p-4 rounded-lg">
                  <p className="text-sm text-gray-600 mb-2">Nomor Virtual Account:</p>
                  <div className="flex items-center gap-2">
                    <code className="bg-white px-4 py-2 rounded flex-1">
                      {virtualAccount}
                    </code>
                    <button 
                      onClick={(e) => {
                        e.stopPropagation();
                        copyToClipboard(virtualAccount);
                      }}
                      className="p-2 hover:bg-gray-200 rounded"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                      >
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                      </svg>
                    </button>
                  </div>
                </div>
                {copied && (
                  <div className="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                    <div className="flex items-center">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                      >
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                      </svg>
                      <span className="ml-2">
                        Nomor Virtual Account berhasil disalin!
                      </span>
                    </div>
                  </div>
                )}
              </div>
            )}
          </div>

          {/* Minimarket Option */}
          <div
            className="border rounded-lg p-4 cursor-pointer hover:bg-gray-50"
            onClick={() => setPaymentMethod('minimarket')}
          >
            <h3 className="font-semibold">Pembayaran Minimarket</h3>
            {paymentMethod === 'minimarket' && (
              <div className="mt-4 space-y-2">
                <p className="text-gray-600">Kode Pembayaran:</p>
                <div className="bg-gray-50 p-4 rounded-lg">
                  <code className="text-lg">{virtualAccount}</code>
                </div>
              </div>
            )}
          </div>
        </div>

        {/* Confirmation Button */}
        {paymentMethod && (
          <button
            onClick={handlePayment}
            className="w-full mt-6 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors"
          >
            Konfirmasi Pembayaran
          </button>
        )}
      </div>
    </div>
  );
}

export default CargoPaymentSystem;