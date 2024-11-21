// Import external libraries
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { SplitText } from "gsap/SplitText";

// Register plugins
gsap.registerPlugin(ScrollTrigger, SplitText);

// Ensure the title element exists and split text if it does
let split;
if (document.querySelector("#title")) {
    split = new SplitText("#title");
}

// GSAP animations with ScrollTrigger
// Parallax effect on #cover
gsap.from("#cover", {
    scrollTrigger: {
        scrub: true,
    },
    y: 80,
    ease: "none", // Adding no easing for parallax smoothness
});

// Left fish animation
gsap.from("#leftfish", {
    scrollTrigger: {
        scrub: true,
    },
    x: -150,
    ease: "none", // Smoother scroll effect
});

// Right fish animation
gsap.from("#rightfish", {
    scrollTrigger: {
        scrub: true,
    },
    x: 200,
    ease: "none", // Smoother scroll effect
});

// Text animation with SplitText
if (split) {
    gsap
        .timeline({
            scrollTrigger: {
                trigger: ".parallax",
                start: "top 70%", // Start the animation when the element is 70% in view
                end: "bottom top",
                toggleActions: "restart none none reset", // Allows it to reset after scrolling past
            },
        })
        .from(split.chars, {
            yPercent: -250,
            stagger: 0.08,
            duration: 0.7,
            ease: "back.out(1.7)", // Ease for a nice pop-out effect
        })
        .from(
            split.chars,
            {
                opacity: 0,
                delay: 0.05,
                stagger: 0.05,
                duration: 0.2,
                ease: "power1.inOut",
            },
            0
        ); // Fade-in effect for the text
}

// Vanilla JS Scroll Event for Title opacity and transforms
window.addEventListener("scroll", function () {
    const header = document.querySelector("header");
    header.classList.toggle("scrolled", window.scrollY > 50);

    // Parallax effect
    const title = document.querySelector("#title");
    const cover = document.querySelector("#cover");
    const leftFish = document.querySelector("#leftfish");
    const rightFish = document.querySelector("#rightfish");

    let scrollValue = window.scrollY;

    // Apply opacity and movement to the title
    title.style.opacity = 1 - scrollValue * 0.003;
    title.style.transform = `translateY(${scrollValue * 0.5}px)`;

    // Apply transformations to the cover and fish images
    cover.style.transform = `translateY(${scrollValue * 0.3}px)`;
    leftFish.style.transform = `translateX(${-scrollValue * 0.4}px)`;
    rightFish.style.transform = `translateX(${scrollValue * 0.4}px)`;
});

// Form Handling with AJAX (fetch)
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("orderForm");

    if (form) {
        form.addEventListener("submit", async (event) => {
            event.preventDefault();

            // Disable submit button to prevent double submission
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Memproses...';

            try {
                // Gather form data
                const formData = new FormData(form);
                const orderDetails = {};

                // Convert FormData to object and validate
                for (const [key, value] of formData.entries()) {
                    orderDetails[key] = value.trim();
                    
                    // Basic validation
                    if (!value.trim()) {
                        throw new Error(`${key} tidak boleh kosong`);
                    }
                }

                // Additional validations
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(orderDetails.email)) {
                    throw new Error('Format email tidak valid');
                }

                if (!/^[0-9]{10,15}$/.test(orderDetails.phone)) {
                    throw new Error('Nomor telepon harus 10-15 digit');
                }

                if (parseInt(orderDetails.jumlah) < 1) {
                    throw new Error('Jumlah pesanan minimal 1');
                }

                // Log data being sent (for debugging)
                console.log('Sending data:', orderDetails);

                // Send data to server
                const response = await fetch("api/order.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(orderDetails)
                });

                // Check if response is ok
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    // Show success message
                    alert("Pesanan Anda berhasil diproses!");
                    // Reset form
                    form.reset();
                } else {
                    throw new Error(data.message || 'Gagal memproses pesanan');
                }

            } catch (error) {
                console.error("Error:", error);
                alert(error.message || "Terjadi kesalahan pada server.");
            } finally {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.textContent = 'Pesan Sekarang';
            }
        });

        // Optional: Add real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                validateField(input);
            });
        });
    }
});

// Field validation function
function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';

    switch (field.name) {
        case 'email':
            isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            errorMessage = 'Format email tidak valid';
            break;
        case 'phone':
            isValid = /^[0-9]{10,15}$/.test(value);
            errorMessage = 'Nomor telepon harus 10-15 digit';
            break;
        case 'jumlah':
            isValid = parseInt(value) > 0;
            errorMessage = 'Jumlah harus lebih dari 0';
            break;
        default:
            isValid = value !== '';
            errorMessage = 'Field ini harus diisi';
    }

    // You can add visual feedback here
    if (!isValid && value !== '') {
        field.style.borderColor = 'red';
        // Optional: Show error message near the field
        const errorDiv = field.parentElement.querySelector('.error-message') || 
                        document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '12px';
        errorDiv.textContent = errorMessage;
        if (!field.parentElement.querySelector('.error-message')) {
            field.parentElement.appendChild(errorDiv);
        }
    } else {
        field.style.borderColor = '';
        const errorDiv = field.parentElement.querySelector('.error-message');
        if (errorDiv) errorDiv.remove();
    }

    return isValid;
}

// Fungsi untuk memuat data penjualan
function loadSalesData() {
    const tableBody = document.getElementById('salesTableBody');
    const loadingElement = document.getElementById('loading');
    const errorElement = document.getElementById('error');
    
    // Tampilkan loading
    loadingElement.style.display = 'block';
    errorElement.style.display = 'none';
    tableBody.innerHTML = ''; // Clear any previous content in the table

    // Fetch data dari API
    fetch('http://localhost/zlfg-store/api/datajual.php') // Pastikan endpoint API benar
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Sembunyikan loading
            loadingElement.style.display = 'none';
            
            // Cek apakah data ada
            if (data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="5">Data tidak ditemukan</td></tr>`;
                return;
            }

            // Tampilkan data dalam tabel
            data.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${item.nama_ikan}</td>
                    <td>${item.bulan}</td>
                    <td>${item.jumlah_penjualan}</td>
                    <td>Rp ${new Intl.NumberFormat('id-ID').format(item.total_penjualan)}</td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            loadingElement.style.display = 'none';
            errorElement.style.display = 'block';
            errorElement.textContent = 'Gagal memuat data penjualan';
        });
}

// Panggil fungsi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Pastikan elemen salesTable ada sebelum memanggil fungsi loadSalesData
    if (document.getElementById('salesTable')) {
        loadSalesData();
    }
});

