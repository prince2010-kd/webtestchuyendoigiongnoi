// Nút tìm kiếm đầu trang chủ
document.querySelector("#btn-search").addEventListener("click", function (e) {
    e.preventDefault();
    document.querySelector("#search").classList.toggle("show-search");
});

// Ẩn biểu tượng
document.addEventListener("DOMContentLoaded", function () {
    const topbar = document.getElementById("topbar");
    const navbar = document.getElementById("mainNavbar");

    window.addEventListener("scroll", function () {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;

        if (scrollTop < 20) {
            // Gần đầu trang: hiện topbar
            topbar.style.transform = "translateY(0)";
            navbar.style.top = "40px";
        } else {
            // Còn lại: ẩn topbar
            topbar.style.transform = "translateY(-100%)";
            navbar.style.top = "0";
        }
    });
});

document
    .querySelector("#contact-widget .contact-button")
    .addEventListener("click", function () {
        document.getElementById("contact-widget").classList.toggle("active");
    });

document.addEventListener("DOMContentLoaded", () => {
    const icon = document.getElementById("main-contact-icon");
    const icons = ["fa-comments", "fa-paper-plane", "fa-bell", "fa-smile"]; // danh sách icon
    let currentIndex = 0;

    setInterval(() => {
        icon.style.opacity = 0;

        setTimeout(() => {
            icon.classList.remove(...icons); // xoá tất cả icon cũ
            icon.classList.add(icons[currentIndex]);
            icon.style.opacity = 1;

            currentIndex = (currentIndex + 1) % icons.length; // chuyển sang icon kế tiếp
        }, 500);
    }, 2000);
});

// Câu chuyện thành công
$(document).ready(function () {
    $(".story-slider").slick({
        slidesToShow: 4, // Số card hiển thị mỗi lần
        slidesToScroll: 1, // Mỗi lần trượt 1 card
        arrows: true, // Hiện nút trái/phải
        dots: true, // Hiện chấm nhỏ bên dưới
        responsive: [
            {
                breakpoint: 992, // Dưới 992px
                settings: {
                    slidesToShow: 2,
                },
            },
            {
                breakpoint: 768, // Dưới 768px
                settings: {
                    slidesToShow: 1,
                },
            },
        ],
    });
});

// Tin tức sự kiện
$(document).ready(function () {
    $(".news-slider").slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        arrows: true,
        dots: true,
        autoplay: false,
        autoplaySpeed: 3000,
        responsive: [
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 2,
                },
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 1,
                },
            },
        ],
    });
});

// Blog
$(document).ready(function () {
    $(".news-blog").slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        arrows: true,
        dots: true,
        autoplay: false,
        autoplaySpeed: 3000,
        responsive: [
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 2,
                },
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 1,
                },
            },
        ],
    });
});

// Hình ảnh hoạt động
$(document).ready(function () {
    $(".slider-hoatdong").slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        arrows: true,
        dots: true,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 3000,
        responsive: [
            {
                breakpoint: 1200,
                settings: { slidesToShow: 4 },
            },
            {
                breakpoint: 992,
                settings: { slidesToShow: 3 },
            },
            {
                breakpoint: 768,
                settings: { slidesToShow: 2 },
            },
            {
                breakpoint: 576,
                settings: { slidesToShow: 1 },
            },
        ],
    });
});

// Nút tải ngay
const btnTaiNgay = document.getElementById("btnTaiNgay");
const popupOverlay = document.getElementById("popupFormOverlay");
const closePopup = document.getElementById("closePopup");
const form = document.querySelector("#popupFormOverlay form");

btnTaiNgay.addEventListener("click", () => {
    popupOverlay.classList.remove("d-none");
});

closePopup.addEventListener("click", () => {
    popupOverlay.classList.add("d-none");
    if (form) form.reset();
});

// Đóng popup khi click ra ngoài form
popupOverlay.addEventListener("click", (e) => {
    if (e.target === popupOverlay) {
        popupOverlay.classList.add("d-none");
        if (form) form.reset();
    }
});

// Ẩn topbar
window.addEventListener("scroll", function () {
    const nav = document.getElementById("navbarContainer");
    nav.classList.toggle("scrolled", window.scrollY > 10);
});

// Trang tin tức sự kiện

// Liên hệ
const diaChiChiTiet = {
    hanoi: ["Huyện Đông Anh", "Bắc Từ Liêm"],
    haiduong: ["TP. Hải Dương", "TP. Chí Linh"],
    bacninh: ["TP. Từ Sơn"],
    vinhphuc: ["TP. Vĩnh Yên"],
    nghean: ["TP. Vinh"],
};

const selectDiaChi = document.getElementById("selectDiaChi");
const selectChiTiet = document.getElementById("selectChiTiet");

if (selectDiaChi && selectChiTiet) {
    selectDiaChi.addEventListener("change", function () {
        const selected = this.value;

        // Xoá các option cũ
        selectChiTiet.innerHTML =
            '<option value="">Chọn địa chỉ chi tiết</option>';

        // Nếu tỉnh thành có trong danh sách thì thêm quận/huyện
        if (diaChiChiTiet[selected]) {
            diaChiChiTiet[selected].forEach(function (quanHuyen) {
                const option = document.createElement("option");
                option.value = quanHuyen.toLowerCase().replace(/\s+/g, "-");
                option.textContent = quanHuyen;
                selectChiTiet.appendChild(option);
            });
        }
    });
}

// Tìm kiếm trung tâm
const branches = [
    {
        name: "ICE IELTS cơ sở Hoàng Quốc Việt",
        province: "hanoi",
        district: "bắc-từ-liêm",
        address:
            "BT 12 - TT4 Khu Đô Thị Nam Cường, Ngõ 234 Hoàng Quốc Việt, Q. Bắc Từ Liêm, TP. Hà Nội",
        mapEmbed:
            "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.5183901485843!2d105.78269007515372!3d21.051947986981975!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab8554444ac3%3A0x76b44b52541a8ca0!2sICE%20English!5e0!3m2!1svi!2s!4v1749920910856!5m2!1svi!2s",
    },
    {
        name: "ICE IELTS cơ sở Đông Anh",
        province: "hanoi",
        district: "huyện-đông-anh",
        address: "183 - 185, Tổ 3, Thị Trấn Đông Anh, Huyện Đông Anh, Hà Nội",
        mapEmbed:
            "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3721.4206413266197!2d105.84175507515565!3d21.1356519840988!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135010038f006bd%3A0xe7faf43223a8d7f!2zSUNFIElFTFRTIMSQw7RuZyBBbmg!5e0!3m2!1svi!2s!4v1749920866332!5m2!1svi!2s",
    },
    {
        name: "ICE IELTS cơ sở Hải Dương",
        province: "haiduong",
        district: "tp.-hải-dương",
        address:
            "Tầng 3+4, Tòa Nhà Thương Mại Số 176 Đường Trường Chinh, TP. Hải Dương",
        mapEmbed:
            "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3726.655864219569!2d106.2955194751507!3d20.926160691294893!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31359b691946eae7%3A0x4aa88ba8ff18eb4c!2zSUNFIElFTFRTIEjhuqNpIETGsMahbmc!5e0!3m2!1svi!2s!4v1749920817096!5m2!1svi!2s",
    },
    {
        name: "ICE IELTS cơ sở Chí Linh",
        province: "haiduong",
        district: "tp.-chí-linh",
        address: "169 Trần Nguyên Đán, Phường Cộng Hoà, TP Chí Linh, Hải Dương",
        mapEmbed:
            "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2212.9353480102363!2d106.39206617848474!3d21.124571044603712!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135799f4683e73b%3A0xc337055d0e3adca5!2sICE%20IELTS%20Ch%C3%AD%20Linh!5e0!3m2!1svi!2s!4v1749920756933!5m2!1svi!2s",
    },
    {
        name: "ICE IELTS cơ sở Vĩnh Yên",
        province: "vinhphuc",
        district: "tp.-vĩnh-yên",
        address: "TTTM Chùa Hà Tiên, Liên Bảo, TP. Vĩnh Yên, Vĩnh Phúc",
        mapEmbed:
            "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7433.641473969888!2d105.59357234194664!3d21.318098566381572!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3134e5cc77e92fb7%3A0x65145a50fda35581!2zSUNFIFbEqW5oIFnDqm4!5e0!3m2!1svi!2s!4v1749920975410!5m2!1svi!2s",
    },
    {
        name: "ICE IELTS cơ sở Từ Sơn",
        province: "bacninh",
        district: "tp.-từ-sơn",
        address:
            "LK 4 - Lô 41,42 KĐT Mới Trang Hạ, Trang Liệt, TP. Từ Sơn, Bắc Ninh",
        mapEmbed:
            "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3721.939745126945!2d105.95066132515518!3d21.11496828481232!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313507c753b8aedf%3A0xcdb420feb8562c89!2zSUNFIElFTFRTIFThu6sgU8ahbg!5e0!3m2!1svi!2s!4v1749921028181!5m2!1svi!2s",
    },
    {
        name: "ICE IELTS cơ sở Nghệ An",
        province: "nghean",
        district: "tp.-vinh",
        address:
            "Số 130 đường Nguyễn Sỹ Sách, Phường Hưng Phúc, TP. Vinh, Nghệ An",
        mapEmbed:
            "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d120912.7008277978!2d105.55777322426097!3d18.730211059396805!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3139cd6a55e897b3%3A0x9b1bb116e48e1762!2sICE%20IELTS%20Vinh!5e0!3m2!1svi!2s!4v1749921064295!5m2!1svi!2s",
    },
];

const btnFilter = document.querySelector(".btn-primary");

if (btnFilter) {
    btnFilter.addEventListener("click", function () {
        const selectDiaChi = document.getElementById("selectDiaChi");
        if (!selectDiaChi) return;

        const province = selectDiaChi.value;
        const district = document.getElementById("selectChiTiet").value;
        const container = document.querySelector(".col-md-5"); // nơi chứa danh sách
        const listContainer = container.querySelectorAll(".branch-info");
        const countElement = document.getElementById("soTrungTam");

        // Xóa các trung tâm cũ
        listContainer.forEach((el) => el.remove());

        // Lọc theo tỉnh và quận/huyện (nếu có)
        const filtered = branches.filter((branch) => {
            if (!province) return true;
            if (province && !district) return branch.province === province;
            return branch.province === province && branch.district === district;
        });

        // Hiển thị số lượng trung tâm
        if (countElement) {
            countElement.textContent = filtered.length;
        }

        // Cập nhật bản đồ với trung tâm đầu tiên nếu có
        const iframe = document.querySelector(".map-container iframe");
        if (iframe && filtered.length > 0 && filtered[0].mapEmbed) {
            iframe.src = filtered[0].mapEmbed;
        }

        // Tạo lại danh sách trung tâm
        filtered.forEach((branch) => {
            const html = `
                <div class="branch-info mb-2 small border rounded p-2 bg-light">
                    <div class="fw-semibold">${branch.name}</div>
                    <div><i class="fas fa-location-dot me-1 text-danger"></i>${branch.address}</div>
                    <div><i class="fas fa-phone me-1 text-danger"></i>02485888383</div>
                    <div><i class="fas fa-envelope me-1 text-danger"></i>info@ieltsice.com</div>
                </div>
            `;
            container.insertAdjacentHTML("beforeend", html);
        });
    });
}

// Khi DOM đã sẵn sàng, thiết lập bản đồ mặc định
window.addEventListener("DOMContentLoaded", function () {
    const iframe = document.querySelector(".map-container iframe");
    const macDinh = branches.find((b) => b.name.includes("Đông Anh"));
    if (iframe && macDinh) {
        iframe.src = macDinh.mapEmbed;
    }
});

// Nút đăng ký
document.addEventListener("DOMContentLoaded", function () {
    const btnDangKy = document.querySelector(".btn-dangky");
    btnDangKy?.addEventListener("click", function (e) {
        e.preventDefault();

        const formSection = document.getElementById("formDangKy");
        if (formSection) {
            formSection.scrollIntoView({
                behavior: "smooth",
            });
        }
    });
});

// Gửi form google sheet
function handleFormSubmit(event) {
    event.preventDefault();

    const form = document.getElementById("formTuVan");
    const formData = new FormData(form);
    const submitUrl = form.dataset.submitUrl;

    fetch(submitUrl, {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": form.querySelector('input[name="_token"]').value,
        },
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.status === "ok") {
                form.reset();
                document.getElementById("formSuccessMessage").style.display =
                    "block";
            }
        })
        .catch((error) => {
            console.error("Lỗi gửi form:", error);
        });

    return false;
}

// Gửi form google sheet popup
function handlePopupFormSubmit(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    const submitUrl = form.dataset.submitUrl;

    fetch(submitUrl, {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": form.querySelector('input[name="_token"]').value,
        },
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.status === "ok") {
                form.reset();

                // Ẩn popup
                const popup = document.getElementById("popupFormOverlay");
                if (popup) popup.classList.add("d-none");

                // Hiển thị toast thành công
                if (typeof toastr !== "undefined") {
                    toastr.success("Gửi thông tin thành công!");
                } else {
                    alert("Gửi thành công!");
                }
            }
        })
        .catch((err) => {
            console.error("Lỗi gửi form popup:", err);
            toastr?.error("Đã xảy ra lỗi khi gửi!");
        });

    return false;
}

// Xử lý bình luận
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("commentForm");
    const urlInput = document.getElementById("comment_post_url");
    const commentList = document.getElementById("commentList");

    if (!form || !urlInput || !commentList) return;

    const url = urlInput.value;
    const csrfTokenInput = document.querySelector('input[name="_token"]');
    if (!csrfTokenInput) {
        console.warn("CSRF token không tìm thấy");
        return;
    }
    const csrfToken = csrfTokenInput.value;

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        try {
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formData,
            });

            if (!response.ok) {
                const text = await response.text();
                console.error("Lỗi HTTP:", response.status, text);
                throw new Error("Lỗi server");
            }

            const result = await response.json();

            if (result.success) {
                if (result.comment.approved) {
                     const avatarUrl = "/frontend/asset/images/default-avatar.png";
              const date = new Date(result.comment_time);
              const dateStr = date.toLocaleDateString("vi-VN", {
                  timeZone: "Asia/Ho_Chi_Minh",
              });
              const timeStr = date.toLocaleTimeString("vi-VN", {
                  timeZone: "Asia/Ho_Chi_Minh",
                  hour12: false,
                  hour: "2-digit",
                  minute: "2-digit",
              });
              const vnTime = `${dateStr} ${timeStr}`;
                const newComment = `
                <div class="mb-4 border-bottom pb-3 comment-item">
                  <div class="d-flex align-items-start">
                  <img src="${avatarUrl}" alt="Avatar" class="rounded-circle me-3" width="50" height="50">
                      <div>
                          <strong>${escapeHtml(
                              result.comment.name
                          )}</strong><br>
                          <span class="text-muted small">${vnTime}</span>
                          <p class="mb-1 mt-2">${escapeHtml(
                              result.comment.comment
                          )}</p>
                      </div>
                  </div>
                </div>
                `;
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = newComment.trim();
                const commentElement = tempDiv.firstElementChild;

                const firstComment = commentList.querySelector(".comment-item");
                if (firstComment) {
                    commentList.insertBefore(commentElement, firstComment);
                } else {
                    commentList.appendChild(commentElement);
                }
                commentList.lastElementChild.scrollIntoView({
                    behavior: "smooth",
                });
                const commentCountElement = document.getElementById("commentCount");
                if (commentCountElement) {
                    const currentText = commentCountElement.textContent;
                    const match = currentText.match(/\d+/);
                    if (match) {
                        const currentCount = parseInt(match[0]);
                        const newCount = currentCount + 1;
                        commentCountElement.textContent = `Bình luận (${newCount})`;
                    }
                }
                }
             
                form.reset();
                alert("Cảm ơn bạn đã gửi bình luận! ");
            } else {
                alert("Lỗi: " + (result.message || "Không thể gửi bình luận."));
            }
        } catch (error) {
            console.error(error);
            alert("Đã xảy ra lỗi khi gửi bình luận!");
        }
    });

    function escapeHtml(text) {
        const div = document.createElement("div");
        div.innerText = text;
        return div.innerHTML;
    }
});

// Xử lý thu gọn, mở rộng bình luận
document.addEventListener("DOMContentLoaded", function () {
    const loadMoreBtn = document.getElementById("loadMoreComments");
    const collapseBtn = document.getElementById("collapseComments");

    if (!loadMoreBtn || !collapseBtn) return;

    loadMoreBtn.addEventListener("click", function () {
        document.querySelectorAll(".comment-item.d-none").forEach((comment) => {
            comment.classList.remove("d-none");
        });
        loadMoreBtn.classList.add("d-none");
        collapseBtn.classList.remove("d-none");
    });

    collapseBtn.addEventListener("click", function () {
        const allComments = document.querySelectorAll(".comment-item");
        allComments.forEach((comment, index) => {
            if (index >= 2) {
                comment.classList.add("d-none");
            }
        });
        collapseBtn.classList.add("d-none");
        loadMoreBtn.classList.remove("d-none");
    });
});

document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.language-option').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const lang = this.getAttribute('data-lang');
                const select = document.querySelector('.goog-te-combo');
                if (select) {
                    select.value = lang;
                    select.dispatchEvent(new Event('change'));
                }
            });
        });
    });

document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".youtube-lazy").forEach(function (el) {
            el.addEventListener("click", function () {
                const id = el.dataset.id;
                const iframe = document.createElement("iframe");
                iframe.setAttribute("src", "https://www.youtube.com/embed/" + id + "?autoplay=1");
                iframe.setAttribute("frameborder", "0");
                iframe.setAttribute("allow", "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share");
                iframe.setAttribute("allowfullscreen", "");
                iframe.className = "w-100 h-100";
                el.innerHTML = '';
                el.appendChild(iframe);
            });
        });
    });


    // document.getElementById('start-test-btn').addEventListener('click', function () {
    //     document.getElementById('test-questions-section').style.display = 'block';
    //     this.style.display = 'none';
    //     window.scrollTo({ top: document.getElementById('test-questions-section').offsetTop, behavior: 'smooth' });

    //     startCountdown(15 * 60); // 15 phút
    // });

    // // Hàm đếm ngược
    // function startCountdown(duration) {
    //     let timer = duration;
    //     const display = document.getElementById('countdown-timer');

    //     const interval = setInterval(function () {
    //         let minutes = Math.floor(timer / 60);
    //         let seconds = timer % 60;

    //         minutes = minutes < 10 ? '0' + minutes : minutes;
    //         seconds = seconds < 10 ? '0' + seconds : seconds;

    //         display.textContent = `${minutes}:${seconds}`;

    //         if (--timer < 0) {
    //             clearInterval(interval);
    //             display.textContent = "Hết giờ!";
    //             alert("Hết thời gian làm bài. Hệ thống sẽ tự động nộp bài.");
    //             document.querySelector('#test-questions-section form').submit();
    //         }
    //     }, 1000);
    // }