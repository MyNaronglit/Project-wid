$(document).ready(function() {
    $.ajax({
        url: "php-backend/manage-index.php",
        method: "GET",
        dataType: "json",
        success: function(data) {
            if (data.length > 0) {
                let indicators = "";
                let carouselItems = "";

                data.forEach((item, index) => {
                    let activeClass = index === 0 ? "active" : "";

                    indicators += `<li data-slide-to="${index}" class="${activeClass}"></li>`;

                    carouselItems += `
                        <div class="item ${activeClass}" style="display: ${index === 0 ? 'block' : 'none'};">
                            <div class="carousel-content">
                                <div class="carousel-image">
                                    <a href="#">
                                        <img width="300" height="200" src="/wdi/www.wdi.co.th/th/adminkit-dev/static/${item.news_image}" alt="${item.news_title}" />
                                    </a>
                                </div>
                                <div class="carousel-content-text">
                                    <h4>${item.news_title}</h4>
                                    <p>${item.news_content.substring(0, 100)}...</p>
                                    <a class="readmore read-news" href="#">Read More</a>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $(".carousel-indicators").html(indicators);
                $(".carousel-inner").html(carouselItems);
            }

            let currentIndex = 0;
            let items = $(".carousel-inner .item");
            let totalItems = items.length;

            function updateCarousel() {
                items.fadeOut().removeClass("active");
                items.eq(currentIndex).fadeIn().addClass("active");

                $(".carousel-indicators li").removeClass("active");
                $(".carousel-indicators li").eq(currentIndex).addClass("active");
            }

            function goToNext() {
                currentIndex = (currentIndex + 1) % totalItems;
                updateCarousel();
            }

            function goToPrev() {
                currentIndex = (currentIndex - 1 + totalItems) % totalItems;
                updateCarousel();
            }

            $(".carousel-control-next").click(function(e) {
                e.preventDefault();
                goToNext();
            });

            $(".carousel-control-prev").click(function(e) {
                e.preventDefault();
                goToPrev();
            });

            setInterval(goToNext, 3000);
        },
        error: function(error) {
            console.error("❌ Error loading carousel data:", error);
        }
    });
});