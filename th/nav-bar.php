<style>
    .login-button-container {
        margin-left: 15px;
        display: inline-block;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
        padding: 8px 16px;
        text-decoration: none;
        border-radius: 4px;
        border: none;
        font-size: 14px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }


    /* เมนูมือถือเริ่มต้นซ่อน */
    #mobile-menu {
        display: none;
    }

    /* เมื่อมี class open ให้แสดง */
    #mobile-menu.open {
        display: block;
    }



    /* ซ่อน toggle ปุ่มบนจอใหญ่ และแสดงเมนูเสมอ */
    @media (min-width: 768px) {
        #mobile-menu {
            display: block !important;
        }

        #custom-navbar-toggle {
            display: none;
        }
    }

    /* ปรับสีปุ่ม dropdown ให้ชัดเจนบนพื้นหลังขาว */
    #mobile-menu button.dropdown-toggle {
        background: transparent;
        border: none;
        color: #ffffff;
        font-size: 20px;
        cursor: pointer;
        padding: 0 8px;
        vertical-align: middle;
        user-select: none;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
    }

    /* ซ่อน submenu ดีฟอลต์ */
    #mobile-menu ul.sub-menu {
        display: none;
        padding-left: 20px;
        margin: 5px 0;
    }

    /* เมื่อ toggle แล้ว ให้แสดง */
    #mobile-menu ul.sub-menu.open {
        display: block;
        width: 240px;
    }

    /* เมนูหลัก: ชิดซ้าย */
    #menu-main-nav>li {

        color: white;
    }

    /* ลิงก์ภายในเมนูหลัก */
    #menu-main-nav>li>a {
        padding-left: 0;
        display: block;
        color: white;
    }

    /* เมนูย่อย: เยื้องเข้ามาหนึ่งแท็บ */
    #menu-main-nav li ul.sub-menu {
        padding-left: 1.5rem;
    }

    /* เมนูย่อยแต่ละรายการ */
    #menu-main-nav li ul.sub-menu li {
        text-align: left;
        padding-left: 0;
    }

    /* ลิงก์ในเมนูย่อย */
    #menu-main-nav li ul.sub-menu li a {
        display: block;
        padding-left: 1rem;
        color: white;
    }

    @media screen and (max-width: 768px) {

        /* ปรับสีปุ่ม dropdown ให้ชัดเจนบนพื้นหลังขาว */
        #mobile-menu button.dropdown-toggle {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 20px;
            cursor: pointer;
            padding: 0 8px;
            vertical-align: middle;
            user-select: none;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }

        /* ซ่อน submenu ดีฟอลต์ */
        #mobile-menu ul.sub-menu {
            display: none;
            padding-left: 20px;
            margin: 5px 0;
        }

        /* เมื่อ toggle แล้ว ให้แสดง */
        #mobile-menu ul.sub-menu.open {
            display: block;
        }

        /* เมนูหลัก: ชิดซ้าย */
        #menu-main-nav>li {
            padding-left: 10;
            margin-left: 0;
            text-align: left;
            color: white;
        }

        /* ลิงก์ภายในเมนูหลัก */
        #menu-main-nav>li>a {
            padding-left: 0;
            display: block;
            color: white;
            font-size: 15px;
        }

        #menu-main-nav>a {

            display: block;
            color: white;
            font-size: 15px;
        }

        /* เมนูย่อย: เยื้องเข้ามาหนึ่งแท็บ */
        #menu-main-nav li ul.sub-menu {
            padding-left: 1.5rem;
        }

        /* เมนูย่อยแต่ละรายการ */
        #menu-main-nav li ul.sub-menu li {
            text-align: left;
            padding-left: 0;
        }

        /* ลิงก์ในเมนูย่อย */
        #menu-main-nav li ul.sub-menu li a {
            display: block;
            padding-left: 1rem;
            color: white;
        }
    }

    .mobile-search-lang {
        display: none;
    }

    /* แสดงบนหน้าจอมือถือ (max-width 767px) */
    @media (max-width: 767px) {
        .mobile-search-lang {
            display: block;
        }
    }
</style>
<style>
    /* เมนูหลัก */
    #menu-main-nav {
        gap: 1.5rem;
        list-style: none;
        margin: 0;
        padding: 0.5rem 1rem;
        font-family: 'Barlow';
        z-index: 10;

    }


    #menu-main-nav>li.menu-item>a {
        padding: 0.75rem 1.2rem;
        color: #fff;
        text-decoration: none;
        transition: all 0.3s ease;
        border-radius: 6px;
    }

    #menu-main-nav>li.menu-item:hover>a {
        background-color: rgb(203, 66, 66);
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0, 188, 212, 0.4);
    }

    #menu-main-nav>li.menu-item.active>a {
        background-color: rgb(203, 66, 66);
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0, 188, 212, 0.4);
    }



    /* กล่อง input */
    .navbar-form .form-control {
        width: 300px;
        max-width: 100%;
        padding: 0.5rem 1rem;
        border: 2px solid #00bcd4;
        border-radius: 25px;
        font-size: 14px;
        color: #333;
        background-color: #fff;
        transition: all 0.3s ease;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    /* เมื่อโฟกัส */
    .navbar-form .form-control:focus {
        outline: none;
        border-color: #0097a7;
        box-shadow: 0 0 5px rgba(0, 188, 212, 0.4);
    }

    /* Responsive สำหรับมือถือ */
    @media (max-width: 768px) {
        .navbar-form .form-control {
            width: 100%;
        }
    }
</style>

<nav class="topnav navbar navbar-inverse navbar-static-top" role="navigation">
    <div class="container hidden-xs">
        <div class="navbar-collapse collapse" id="navbar">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <form class="navbar-form navbar-left" method="get" action="/wdi/www.wdi.co.th/th/Search-page.php" autocomplete="off">
                        <div class="form-group">
                            <input name="s" id="s" type="text" class="form-control" placeholder="Site Search" value="" />
                        </div>
                    </form>
                </li>
                <div class="language" style="float: right; margin-top: 10px;">
                    <ul>
                        <li class="lang-en"><a href="#" hreflang="en" title="English (en)"><img src="https://www.wdi.co.th/wp-content/plugins/qtranslate-x/flags/gb.png" alt="English (en)"><span style="display:none">English</span></a></li>
                        <li class="lang-th active"><a href="#" hreflang="th" title="Thai (th)"><img src="https://www.wdi.co.th/wp-content/plugins/qtranslate-x/flags/th.png" alt="Thai (th)"><span style="display:none">Thai</span></a></li>
                    </ul>
                </div>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="navbar-header" style=" margin-right: -35px; margin-left: -30px;">
            <button type="button" class="navbar-toggle collapsed" id="custom-navbar-toggle" aria-expanded="false" aria-controls="mobile-menu">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar" style="background-color:#ffffff;"></span>
                <span class="icon-bar" style="background-color:#ffffff;"></span>
                <span class="icon-bar" style="background-color:#ffffff;"></span>
            </button>
            <a style="margin-top: -20px;" class="navbar-brand" href="/wdi/www.wdi.co.th/th/index.php">
                <img src="/wdi/www.wdi.co.th/wp-content/uploads/2015/09/WDI_logo.png" width="125" height="50" alt="WDI Logo" />
            </a>
        </div>

        <!-- Mobile Menu Container -->
        <div id="mobile-menu" class="navbar-collapse collapse">

            <!-- Search and language section shown on mobile, above menu -->
            <div class="mobile-search-lang" style="padding:10px 15px; border-bottom: 1px solid #ddd;background-color: black;">
                <form class="navbar-form" method="get" action="/wdi/www.wdi.co.th/th/Search-page.php" autocomplete="off" style="margin-bottom:10px;">
                    <div class="form-group" style="width:100%;">
                        <input name="s" id="s-mobile" type="text" class="form-control" placeholder="Site Search" value="" style="width: 100%;" />
                    </div>
                </form>

                <div class="language" style="margin-top: 10px;">
                    <ul style="padding-left:0; list-style:none; display:flex; gap:10px;">
                        <li class="lang-en"><a href="#" hreflang="en" title="English (en)"><img src="https://www.wdi.co.th/wp-content/plugins/qtranslate-x/flags/gb.png" alt="English (en)"><span style="display:none">English</span></a></li>
                        <li class="lang-th active"><a href="#" hreflang="th" title="Thai (th)"><img src="https://www.wdi.co.th/wp-content/plugins/qtranslate-x/flags/th.png" alt="Thai (th)"><span style="display:none">Thai</span></a></li>
                    </ul>
                </div>
            </div>

            <ul id="menu-main-nav" class="menu">
                <li id="menu-item-502" class="menu-item current-menu-item current_page_item menu-item-home menu-item-502"><a href="/wdi/www.wdi.co.th/th/index.php" data-th="หน้าแรก" data-en="Home">Home</a></li>
                <li id="menu-item-8184" class="menu-item menu-item-has-children menu-item-8184">
                    <a href="/wdi/www.wdi.co.th/th/product/category-select.php" data-th="สินค้า" data-en="Products" style="color: white;">Products</a>
                    <ul class="sub-menu">
                        <li id="menu-item-3565" class="menu-item"><a href="/wdi/www.wdi.co.th/th/product/product-led-lamps.php?category=RElBTU9ORCBSZXBsYWNlbWVudCBQYXJ0cyAgUGlja3VwLCBDYXIgJiBUcnVjaw%3D%3D">DIAMOND Replacement Parts <br> Pickup, Car & Truck</a></li>
                        <li id="menu-item-3566" class="menu-item"><a href="/wdi/www.wdi.co.th/th/product/product-led-lamps.php?category=RElBTU9ORCBSZXBsYWNlbWVudCBQYXJ0cyAgTW90b3JjeWNsZQ%3D%3D">DIAMOND Replacement Parts <br>Motorbike</a></li>
                        <li id="menu-item-3560" class="menu-item"><a href="/wdi/www.wdi.co.th/th/product/product-led-lamps.php?category=TEVEIExpZ2h0aW5n">LED Lighting</a></li>
                        <li id="menu-item-3562" class="menu-item"><a href="/wdi/www.wdi.co.th/th/product/product-led-lamps.php?category=SW5jYW5kZXNjZW50IExpZ2h0aW5n">Incandescent Lighting</a></li>
                        <li id="menu-item-3565" class="menu-item"><a href="/wdi/www.wdi.co.th/th/product/product-led-lamps.php?category=VW5pdmVyc2FsICYgU2FmZXR5IEFjY2Vzc29yaWVz">Universal & Safety Accessories</a></li>
                        <li id="menu-item-3566" class="menu-item"><a href="/wdi/www.wdi.co.th/th/product/product-led-lamps.php?category=QnVsYnM%3D">Bulbs</a></li>
                        <li id="menu-item-3566" class="menu-item"><a href="/wdi/www.wdi.co.th/th/product/product-led-lamps.php?category=RklUVCBWZWhpY2xlIFN0eWxpbmcgQWNjZXNzb3JpZXM%3D">FITT Vehicle Styling Accessories</a></li>
                        <li id="menu-item-3561" class="menu-item"><a href="/wdi/www.wdi.co.th/th/product/product-led-lamps.php?category=RkFDTElURSBJbmR1c3RyaWFsIExpZ2h0aW5n">FACLITE Industrial Lighting</a></li>
                    </ul>
                </li>
                <li id="menu-item-71" class="menu-item"><a href="/wdi/www.wdi.co.th/th/about.php" data-th="เกี่ยวกับ WDI" data-en="About Us">About Us</a></li>
                <li id="menu-item-71" class="menu-item"><a href="/wdi/www.wdi.co.th/th/News-page.php" data-th="ข่าวสาร" data-en="News">News</a></li>
                <li id="menu-item-72" class="menu-item"><a href="/wdi/www.wdi.co.th/th/media/media.php" data-th="มีเดีย" data-en="Media">Media</a></li>
                <li id="menu-item-74" class="menu-item"><a href="/wdi/www.wdi.co.th/th/map/mapShow.php" data-th="ติดต่อฉัน" data-en="Contact Us">Contact Us</a></li>
            </ul>
        </div>
    </div>
</nav>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        function setLanguage(lang) {
            localStorage.setItem('language', lang);
            document.cookie = "language=" + lang + "; path=/"; // ✅ sync กับ PHP ผ่าน cookie
            const isThai = lang === 'th';

            document.querySelectorAll('#menu-main-nav a[data-th][data-en]').forEach(el => {
                el.textContent = isThai ? el.dataset.th : el.dataset.en;
            });

            document.querySelectorAll('h2[data-th][data-en]').forEach(el => {
                el.textContent = isThai ? el.dataset.th : el.dataset.en;
            });
            document.querySelectorAll('.lang-th').forEach(el => {
                el.classList.toggle('active', isThai);
            });
            document.querySelectorAll('.lang-en').forEach(el => {
                el.classList.toggle('active', !isThai);
            });
        }

        const savedLang = localStorage.getItem('language') || 'th';
        setLanguage(savedLang);

        document.querySelectorAll('.lang-th a').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                setLanguage('th');
            });
        });

        document.querySelectorAll('.lang-en a').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                setLanguage('en');
            });
        });
    });


    document.addEventListener("DOMContentLoaded", function() {
        const currentPath = window.location.pathname;
        const menuLinks = document.querySelectorAll("#menu-main-nav a");

        menuLinks.forEach(link => {
            const linkPath = new URL(link.href).pathname;

            if (linkPath === currentPath || currentPath.startsWith(linkPath)) {
                // หา <li> ที่เป็น parent ใกล้สุดของ <a>
                const parentLi = link.closest("li");
                if (!parentLi) return;

                // ตรวจสอบว่า parent <li> นี้ เป็น child ตรงของ <ul id="menu-main-nav"> หรือไม่ (เมนูหลัก)
                const parentUl = parentLi.parentElement;
                if (parentUl && parentUl.id === "menu-main-nav") {
                    // ใส่ class active ให้ <a> และ <li> เฉพาะเมนูหลักเท่านั้น
                    link.classList.add("active");
                    parentLi.classList.add("active");
                }
            }
        });
    });


    document.addEventListener("DOMContentLoaded", function() {
        // ตรวจสอบว่ากำลังอยู่ในหน้าจอมือถือ (ความกว้าง <= 768px)
        if (window.matchMedia("(max-width: 999999px)").matches) {
            const toggleButton = document.getElementById("custom-navbar-toggle");
            const mobileMenu = document.getElementById("mobile-menu");

            toggleButton.addEventListener("click", function() {
                mobileMenu.classList.toggle("open");
                const expanded = toggleButton.getAttribute("aria-expanded") === "true";
                toggleButton.setAttribute("aria-expanded", !expanded);
            });

            // เพิ่มปุ่ม dropdown ใน <li> แทนหลัง <a>
            const menuItemsWithChildren = document.querySelectorAll("#mobile-menu li.menu-item-has-children");

            menuItemsWithChildren.forEach(item => {
                const anchor = item.querySelector("a");
                const submenu = item.querySelector("ul.sub-menu");
                if (!anchor || !submenu) return;

                // สร้าง wrapper สำหรับ anchor และปุ่ม
                const wrapper = document.createElement("div");

                wrapper.style.alignItems = "center";
                wrapper.style.justifyContent = "space-between";
                wrapper.style.position = "relative";

                // ย้าย anchor เข้า wrapper
                anchor.parentNode.insertBefore(wrapper, anchor);
                wrapper.appendChild(anchor);

                // สร้างปุ่ม dropdown
                const dropdownBtn = document.createElement("button");
                dropdownBtn.classList.add("dropdown-toggle");
                dropdownBtn.setAttribute("aria-expanded", "false");
                dropdownBtn.setAttribute("aria-label", "Toggle submenu");
                dropdownBtn.innerText = "▾";

                // ใส่ปุ่มใน wrapper
                wrapper.appendChild(dropdownBtn);

                // ซ่อน submenu เริ่มต้น
                submenu.classList.remove("open");
                submenu.style.display = "none";

                // toggle dropdown
                dropdownBtn.addEventListener("click", function(e) {
                    e.preventDefault();
                    const isOpen = submenu.classList.toggle("open");
                    submenu.style.display = isOpen ? "block" : "none";
                    dropdownBtn.setAttribute("aria-expanded", isOpen);
                    dropdownBtn.innerText = isOpen ? "▴" : "▾";
                });
            });
        }
    });
</script>