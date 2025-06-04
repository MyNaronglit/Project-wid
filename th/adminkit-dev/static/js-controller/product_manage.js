$(document).ready(function () {
  let currentSortOrder = "DESC"; // เรียงจากใหม่ไปเก่าเริ่มต้น

  function loadOrders(searchQuery = "", sortOrder = "DESC") {
    $.ajax({
      url: "../back-php/order_manage.php",
      method: "GET",
      data: { action: "fetch_orders", search: searchQuery, sort: sortOrder },
      dataType: "json",
      success: function (response) {
        let orderList = $(".order-list ul");
        orderList.empty();

        if (Array.isArray(response) && response.length === 0) {
          orderList.html(
            '<li class="list-group-item text-center text-muted">No products found.</li>'
          );
        } else if (Array.isArray(response)) {
          response.forEach((order) => {
            let listItem = $(`
                            <li class="list-group-item d-flex justify-content-between align-items-center order-item"
                                data-product_id="${order.product_id}"
                                style="cursor: pointer; transition: background 0.3s;">
                                <div>
                                    <i class="fa-solid fa-box text-primary"></i>
                                    <strong>${order.product_name}</strong>
                                    <span class="text-muted"> (${order.item_number})</span>
                                </div>
                            </li>
                        `);

            // Attach click event for order details
            listItem.click(function () {
              loadOrderDetails(order.product_id);
            });

            orderList.append(listItem);
          });
        } else {
          orderList.html(
            '<li class="list-group-item text-center text-muted">Error: Invalid response data.</li>'
          );
        }
      },
      error: function () {
        $(".order-list ul").html(
          '<li class="list-group-item text-center text-muted">Error fetching products.</li>'
        );
      },
    });
  }

  function loadOrderDetails(productId) {
    $("#order-details").html(
      '<p class="text-center text-muted">Loading...</p>'
    );
    $.ajax({
      url: "../back-php/order_manage.php",
      method: "GET",
      data: { action: "get_order_details", id: productId },
      dataType: "html",
      success: function (response) {
        $("#order-details").html(response);
      },
      error: function () {
        $("#order-details").html(
          '<p class="text-center text-danger">Error loading order details.</p>'
        );
      },
    });
  }

  $(document).on("click", ".edit-product", function (e) {
    e.preventDefault();
    const productId = $(this).data("product_id");

    $.ajax({
      url: "../back-php/order_manage.php",
      method: "GET",
      data: { action: "get_edit_product", id: productId },
      dataType: "json",
      success: function (data) {
        const product = data.product || {};
        const RefID_img = data.product.RefID_img || "";
        const allProducts = Array.isArray(data.all_products)
          ? data.all_products
          : [];
        const detailImgs = Array.isArray(product.detail_images)
          ? product.detail_images
          : [];
        const funcImgs = product.product_func_image
          ? product.product_func_image.split(",")
          : [];
        // สร้าง options สำหรับ select ให้ reuse ได้
        const makeOptions = (arr, current) => {
          const unique = [...new Set(arr)];
          return ['<option value="">Select</option>']
            .concat(
              unique.map(
                (v) =>
                  `<option value="${v}" ${
                    v === current ? "selected" : ""
                  }>${v}</option>`
              )
            )
            .join("");
        };

        const categoryOptions = makeOptions(
          allProducts.map((p) => p.category),
          product.category
        );
        const categoryDetailOptions = makeOptions(
          allProducts.map((p) => p.category_detail),
          product.category_detail
        );

        let formDataToSubmit;

        Swal.fire({
          title: `Edit Product #${productId}`,
          customClass: { popup: "w-full max-w-7xl" },
          width: "90%",
          html: `
          <div class="form-container">

            <div class="form-box">
              <h2>🛒 Product Info</h2>
              <div class="form-group">
                <label>Category</label>
                <div class="btn-group">
                  <button type="button" id="category_select" class="btn btn-primary">Select</button>
                  <button type="button" id="category_input" class="btn btn-secondary">Input</button>
                </div>
                <select id="category_select_element" class="mb-2">${categoryOptions}</select>
                <input id="category_input_element" class="hidden" placeholder="Enter category" value="${
                  product.category || ""
                }">
              </div>
              
              <!-- brand/model -->
              <div class="form-group">
                <label id="car_brand_input_label">Upload brand</label>
                <input id="car_brand_input" placeholder="Enter car brand" class="hidden" value="${
                  product.car_brand_input || ""
                }">
                <div id="upload_section_brand" class="hidden">
                  <input type="file" id="car_image_upload_brand">
                </div>
              </div>
              <div class="form-group">
                <label id="car_model_input_label">Upload model</label>
                <input id="car_model_input" placeholder="Enter car model" class="hidden" value="${
                  product.car_model_input || ""
                }">
                <div id="upload_section" class="hidden">
                  <input type="file" id="car_image_upload">
                </div>
              </div>

              <div class="form-group">
                <label>Category Detail</label>
                <div class="btn-group">
                  <button type="button" id="category_detail_select" class="btn btn-primary">Select</button>
                  <button type="button" id="category_detail_input" class="btn btn-secondary">Input</button>
                </div>
                <select id="category_detail_select_element" class="hidden">${categoryDetailOptions}</select>
                <input id="category_detail_input_element" class="hidden" placeholder="Enter detail" value="${
                  product.category_detail || ""
                }">
              </div>

              <div class="form-group">
                <label for="manual_pdf_input">Upload MANUAL PDF</label>
                <input type="file" id="manual_pdf_input">
                ${
                  product.manual_pdf
                    ? `<div class="mt-2 text-sm text-blue-600">
                        📎 <a href="../back-php/${product.manual_pdf}" target="_blank" class="underline">View existing PDF</a>
                      </div>`
                    : ""
                }
              </div>


              <div class="form-group">
                <label>YouTube Links</label>
                <div id="youtube_links">
                  ${(product.youtube_links || "")
                    .split(",")
                    .map(
                      (link) => `
                    <div class="youtube-link-group">
                      <input type="text" name="youtube_links[]" class="form-control mb-2" value="${link}" placeholder="Enter YouTube link">
                    </div>
                  `
                    )
                    .join("")}
                </div>
                <button type="button" class="btn btn-sm btn-info mt-1" id="add_youtube_link">เพิ่มลิงก์</button>
              </div>
            </div>

            <div class="form-box">
              <h2>⚙️ Additional Info</h2>
              <div class="form-group">
                <label>Name</label>
                <input id="product_name" placeholder="Enter product name" value="${
                  product.product_name || ""
                }">
              </div>
              <div class="form-group">
                <label>SKU</label>
                <input id="item_number" placeholder="Enter SKU" value="${
                  product.item_number || ""
                }">
              </div>
              <div class="form-group">
                <label>Image</label>
                ${
                  product.image_path
                    ? `<img src="../back-php/${product.image_path}" class="rounded-lg mb-2 " style="max-width:150px;">`
                    : ``
                }
                <input type="file" id="image" accept="image/*">
              </div>

              <div class="form-group">
                <label for="content_en">ข้อมูลสินค้าภาษาอังกฤษ</label>
                <textarea id="content_en" class="summernote" rows="5" name="content_en">${
                  product.Product_content_en || ""
                }</textarea>
              </div>
              <div class="form-group">
                <label for="content_th">ข้อมูลสินค้าภาษาไทย</label>
                <textarea id="content_th" class="summernote" rows="5" name="content_th">${
                  product.Product_content_th || ""
                }</textarea>
              </div>

              <div id="existing_func_images" class="flex flex-wrap gap-2 mb-2">
                      ${funcImgs
                        .map(
                          (img, idx) => `
                        <div class="relative w-24 h-24" data-func-index="${idx}">
                          <img src="../back-php/${img}" class="w-full h-full object-cover rounded" style="max-width:150px;" />
                        </div>
                      `
                        )
                        .join("")}
                    </div>
                    <input type="file" id="product_func_image" multiple />
                    <div id="preview_func_images" class="flex flex-wrap gap-2 mt-2"></div>

              <div class="form-group">
                  <label>More Images</label>
                <div id="existing_detail_images" class="grid grid-cols-2 gap-4 mb-4">
                    ${(Array.isArray(product.detail_images)
                      ? product.detail_images
                      : []
                    )
                      .map(
                        (img) => `
                            <div class="relative aspect-square rounded overflow-hidden border border-gray-200" data-detail-id="${img.detail_img_id}">
                                <h5>${img.detail_img_id}</h5> <img src="../back-php/${img.detail_img_product}" class=" object-cover" style="max-width:150px;" />
                                <button type="button" style="background-color: rgb(171 14 14) !important;"class="absolute top-1 right-1 text-red-600 rounded-full p-1 shadow remove-detail hover:bg-red-100">
                                    &times;
                                </button>
                            </div>
                            `
                      )
                      .join("")}
                </div>
              <input type="file" id="image_details" accept="image/*" multiple />
              <div id="preview_detail_images" class="flex flex-wrap gap-2 mt-2"style="max-width:150px;"></div>
            </div>

            </div>

          </div>
        `,
          showCancelButton: true,
          confirmButtonText: "💾 Save Changes",
          cancelButtonText: "Cancel",
          didOpen: () => {
            setupFormEventListeners();

            // Summernote init
            $(".summernote").summernote({
              placeholder: "พิมพ์เนื้อหาข่าวที่นี่...",
              tabsize: 2,
              height: 300,
              toolbar: [
                ["style", ["style"]],
                ["font", ["bold", "italic", "underline", "clear"]],
                ["fontname", ["fontname"]],
                ["fontsize", ["fontsize"]],
                ["color", ["forecolor", "backcolor"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["insert", ["link", "picture", "video"]],
                ["view", ["fullscreen", "codeview", "help"]],
              ],
            });

            // YouTube link add-button
            $("#add_youtube_link").on("click", () => {
              $("#youtube_links").append(`
              <div class="youtube-link-group">
                <input type="text" name="youtube_links[]" class="form-control mb-2" placeholder="Enter YouTube link">
              </div>`);
            });
            setupDetailImageHandlers();
          },
          preConfirm: () => {
            const fd = validateAndCollectFormData();
            fd.append("action", "update_product");
            fd.append("product_id", productId);
            fd.append("RefID_img", RefID_img);

            const funcImages = $("#product_func_image")[0].files;
            for (let i = 0; i < funcImages.length; i++) {
              fd.append("product_func_image[]", funcImages[i]);
            }

            const detailImages = $("#image_details")[0].files;
            for (let i = 0; i < detailImages.length; i++) {
              fd.append("image_details[]", detailImages[i]);
            }
            window.getRemovedDetailImgIds().forEach((id) => {
              fd.append("remove_detail_img_ids[]", id);
            });
            formDataToSubmit = fd;
            return true;
          },
        }).then((result) => {
          if (result.isConfirmed && formDataToSubmit) {
            $.ajax({
              url: "../back-php/order_manage.php",
              method: "POST",
              data: formDataToSubmit,
              contentType: false,
              processData: false,
              success(resp) {
                Swal.fire(
                  "✅ Updated!",
                  "Product has been updated.",
                  "success"
                ).then(() => location.reload());
              },
              error(xhr, status, error) {
                Swal.fire("❌ Error", "Failed to update product.", "error");
              },
            });
          }
        });
      }, // success
      error: function (xhr, status, error) {
        Swal.fire("Error", "Unable to load product details.", "warning");
      },
    });
  });

  function setupDetailImageHandlers() {
    // ===== Detail Images =====
    const removedDetailImgIds = [];

    // ลบรูป detail เก่า
    $("#existing_detail_images")
      .off("click", ".remove-detail")
      .on("click", ".remove-detail", function () {
        const $box = $(this).closest("[data-detail-id]");
        const id = $box.data("detail-id");
        removedDetailImgIds.push(id);
        $box.remove();
      });

    // preview detail ใหม่
    $("#image_details")
      .off("change")
      .on("change", function () {
        const files = this.files;
        const $preview = $("#preview_detail_images").empty();
        Array.from(files).forEach((file, i) => {
          const reader = new FileReader();
          reader.onload = (e) => {
            const $imgBox = $(`
          <div class="relative w-24 h-24" data-new-index="${i}">
            <img src="${e.target.result}" class="w-full h-full object-cover rounded" style="max-width:150px;" />
            <button type="button" style="background-color: rgb(171 14 14) !important;" class="absolute top-0 right-0 text-red-600  rounded-full p-1 remove-new-detail">×</button>
          </div>
        `);
            $preview.append($imgBox);
          };
          reader.readAsDataURL(file);
        });
      });

    // ลบ preview detail ใหม่
    $("#preview_detail_images")
      .off("click", ".remove-new-detail")
      .on("click", ".remove-new-detail", function () {
        const idx = $(this).parent().data("new-index");
        const input = document.getElementById("image_details");
        const dt = new DataTransfer();
        Array.from(input.files).forEach((file, i) => {
          if (i !== idx) dt.items.add(file);
        });
        input.files = dt.files;
        $(this).parent().remove();
      });

    // ให้เรียกคืน array ของ ID รูป detail ที่ถูกลบ
    window.getRemovedDetailImgIds = () => removedDetailImgIds;

    // ===== Function Images =====

    // ลบรูป function เก่า (no ID tracking)
    $("#existing_func_images")
      .off("click", ".remove-func")
      .on("click", ".remove-func", function () {
        $(this).closest("[data-func-index]").remove();
      });

    // preview function ใหม่
    $("#product_func_image")
      .off("change")
      .on("change", function () {
        const files = this.files;
        const $preview = $("#preview_func_images").empty();
        Array.from(files).forEach((file, i) => {
          const reader = new FileReader();
          reader.onload = (e) => {
            const $imgBox = $(`
          <div class="relative w-24 h-24" data-new-func-index="${i}">
            <img src="${e.target.result}" class="w-full h-full object-cover rounded" style="max-width:150px;" />
            <button type="button" style="background-color: rgb(171 14 14) !important;" class="absolute top-0 right-0 text-red-600 rounded-full p-1 remove-new-func">×</button>
          </div>
        `);
            $preview.append($imgBox);
          };
          reader.readAsDataURL(file);
        });
      });

    // ลบ preview function ใหม่
    $("#preview_func_images")
      .off("click", ".remove-new-func")
      .on("click", ".remove-new-func", function () {
        const idx = $(this).parent().data("new-func-index");
        const input = document.getElementById("product_func_image");
        const dt = new DataTransfer();
        Array.from(input.files).forEach((file, i) => {
          if (i !== idx) dt.items.add(file);
        });
        input.files = dt.files;
        $(this).parent().remove();
      });
  }

  /**
   * Event handler for opening the Add Product modal
   * Creates and displays a form for adding new products to the inventory
   */
  $(document).ready(function () {
    $(document).on("click", "#openModal-addProduct", function () {
      $.ajax({
        url: "../back-php/order_manage.php?action=fetch_orders",
        method: "GET",
        dataType: "json",
        success: function (response) {
          if (response && Array.isArray(response)) {
            const uniqueCategories = [
              ...new Set(
                response
                  .map((p) => p.category)
                  .filter((c) => c && c.trim() !== "")
              ),
            ];
            const uniqueCategoryDetails = [
              ...new Set(
                response
                  .map((p) => p.category_detail)
                  .filter((d) => d && d.trim() !== "")
              ),
            ];

            // Collect item_numbers
            const itemNumbersSet = new Set(response.map((p) => p.item_number));
            // Step 1: Ask for item_number
            Swal.fire({
              title: "กรอกรหัสสินค้า (Item Number)",
              input: "text",
              inputLabel: "ระบุรหัสสินค้าใหม่",
              inputPlaceholder: "เช่น 123456789",
              showCancelButton: true,
              confirmButtonText: "ถัดไป",
              cancelButtonText: "ยกเลิก",
              inputValidator: (value) => {
                if (!value || value.trim() === "") {
                  return "โปรดกรอกรหัสสินค้าก่อน!";
                }
                if (itemNumbersSet.has(value.trim())) {
                  return "Item Number นี้มีอยู่ในระบบแล้ว!";
                }
                return null;
              },
            }).then((result) => {
              if (result.isConfirmed) {
                const newItemNumber = result.value.trim();

                // Step 2: Prepare select options
                let categoryOptions =
                  '<option value="">Select Category</option>';
                uniqueCategories.forEach((category) => {
                  categoryOptions += `<option value="${category}">${category}</option>`;
                });

                let categoryDetailOptions =
                  '<option value="">Select Category Detail</option>';
                uniqueCategoryDetails.forEach((detail) => {
                  categoryDetailOptions += `<option value="${detail}">${detail}</option>`;
                });

                // Step 3: Show full add product form
                Swal.fire({
                  title: "เพิ่มสินค้าใหม่",
                  html: createProductFormHTML(
                    categoryOptions,
                    categoryDetailOptions,
                    newItemNumber
                  ),
                  showCancelButton: true,
                  confirmButtonText: "เพิ่มสินค้า",
                  cancelButtonText: "ยกเลิก",
                  width: "90%",
                  preConfirm: validateAndCollectFormData,
                  didOpen: () => {
                    setupFormEventListeners();
                    setupImageDetailsEventListeners();
                    $(".summernote").summernote({
                      placeholder: "พิมพ์เนื้อหาข่าวที่นี่...",
                      tabsize: 2,
                      height: 300,
                      toolbar: [
                        ["style", ["style"]],
                        ["font", ["bold", "italic", "underline", "clear"]],
                        ["fontname", ["fontname"]],
                        ["fontsize", ["fontsize"]],
                        ["color", ["forecolor", "backcolor"]],
                        ["para", ["ul", "ol", "paragraph"]],
                        ["insert", ["link", "picture", "video"]],
                        ["view", ["fullscreen", "codeview", "help"]],
                      ],
                    });
                    document
                      .getElementById("add_youtube_link")
                      .addEventListener("click", function () {
                        const container =
                          document.getElementById("youtube_links");
                        const newInput = document.createElement("div");
                        newInput.classList.add("youtube-link-group");
                        newInput.innerHTML = `<input type="text" name="youtube_links[]" class="form-control mb-2" placeholder="Enter YouTube link">`;
                        container.appendChild(newInput);
                      });
                  },
                }).then(handleFormSubmission);
              }
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error fetching data",
              text: "Failed to retrieve product data.",
            });
          }
        },
        error: function (xhr, status, error) {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "An error occurred while fetching data.",
          });
        },
      });
    });
  });

  /**
   * Creates the HTML content for the product form
   * @param {string} categoryOptions - HTML string of options for category dropdown
   * @param {string} categoryDetailOptions - HTML string of options for category detail dropdown
   * @returns {string} Complete HTML for the product form
   */
  function createProductFormHTML(categoryOptions, categoryDetailOptions) {
    return `
<div class="country-container">
  <input type="checkbox" id="usa" name="country[]" value="USA">
  <label for="usa" class="country-label">
    <img src="https://flagcdn.com/w320/us.png" alt="USA">
    <span class="country-name">USA</span>
  </label>

  <input type="checkbox" id="th" name="country[]" value="TH">
  <label for="th" class="country-label">
    <img src="https://flagcdn.com/w320/th.png" alt="Thailand">
    <span class="country-name">Thailand</span>
  </label>
</div>
     <div class="form-container">

  <!-- กล่องซ้าย -->
  <div class="form-box">
    <h2>🛒 Product Info</h2>

    <div class="form-group">
      <label>Category</label>
      <div class="btn-group">
        <button type="button" id="category_select" class="btn btn-primary">Select</button>
        <button type="button" id="category_input" class="btn btn-secondary">Input</button>
      </div>
      <select id="category_select_element" class="mb-2">
          ${categoryOptions}
      </select>
      <input id="category_input_element" placeholder="Enter category">
    </div>

    <div class="form-group">
      <label id="car_brand_input_label">Upload brand</label>
      <input id="car_brand_input" placeholder="Enter car brand" class="hidden">
      <div id="upload_section_brand" class="hidden">
        <input type="file" id="car_image_upload_brand">
      </div>
    </div>

    <div class="form-group">
      <label id="car_model_input_label">Upload model</label>
      <input id="car_model_input" placeholder="Enter car model" class="hidden">
      <div id="upload_section" class="hidden">
        <input type="file" id="car_image_upload">
      </div>
    </div>

    <div class="form-group">
      <label id="category_detail_label">Category Detail</label>
      <div class="btn-group">
        <button type="button" id="category_detail_select" class="btn btn-primary">Select</button>
        <button type="button" id="category_detail_input" class="btn btn-secondary">Input</button>
      </div>
      <select id="category_detail_select_element" style="display: none;">
          ${categoryDetailOptions}
      </select>
      <input id="category_detail_input_element" style="display: none;" placeholder="Enter category detail">
    </div><br><br>

     <div class="form-group">
        <label for="manual_pdf_input">Upload MANUAL PDF</label>
        <div id="upload_MANUAL">
          <input type="file" id="manual_pdf_input" accept=".pdf">
        </div>
      </div><br>

     <div class="form-group">
        <label for="ProductSheet_pdf_input">Product Sheet PDF</label>
        <div id="upload_ProductSheet">
          <input type="file" id="ProductSheet_pdf_input" accept=".pdf">
        </div>
      </div><br>


     <div class="form-group">
      <label>YouTube Links</label>
      <div id="youtube_links">
        <div class="youtube-link-group">
          <input type="text" name="youtube_links[]" class="form-control mb-2" placeholder="Enter YouTube link">
        </div>
      </div>
      <button type="button" class="btn btn-sm btn-info mt-1" id="add_youtube_link">เพิ่มลิงก์</button>
    </div>
  </div>


  <!-- กล่องขวา -->
  <div class="form-box">
    <h2>⚙️ Additional Info</h2>

    <div class="form-group">
      <label for="product_name">Name</label>
      <input id="product_name" placeholder="Enter product name">
    </div>

    <div class="form-group">
      <label for="item_number">SKU</label>
      <input id="item_number" placeholder="Enter SKU">
    </div>

    <div class="form-group">
      <label>Image</label>
      <input type="file" id="image" accept="image/*">
    </div>



<div class="form-group">
  <label for="content_en">ข้อมูลสินค้าภาษาอังกฤษ</label>
  <textarea id="content_en" class="summernote" rows="5" name="content_en"></textarea>
</div><br>
<div class="form-group">
  <label for="content_th">ข้อมูลสินค้าภาษาไทย</label>
  <textarea id="content_th" class="summernote" rows="5" name="content_th"></textarea>
</div>


    <div class="form-group">
      <label>Function Image</label>
      <input type="file" id="product_func_image_test" accept="image/*" multiple>
      <ul id="selected_func_image_list"></ul>
    </div>

    <div class="form-group">
      <label>More Images</label>
      <input type="file" id="image_details" accept="image/*" multiple>
      <ul id="selected_images_list"></ul>
    </div>
  </div>

</div>

    `;
  }

  let selectedFuncImageFiles = [];

  function setupImageDetailsEventListeners() {
    // Handling selected function images
    const productFuncImageInput = $("#product_func_image_test")[0];
    const selectedFuncImageList = $("#selected_func_image_list");

    $(productFuncImageInput).on("change", function () {
      const files = this.files;
      if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
          const file = files[i];
          selectedFuncImageFiles.push(file);

          const listItem = $("<li>").addClass(
            "flex items-center justify-between py-2 px-3 rounded-md bg-gray-100 border border-gray-200"
          );

          const fileName = $("<span>").text(file.name);

          const removeButton = $("<button>")
            .text("ลบ")
            .addClass(
              "ml-4 px-3 py-1 bg-red-500 hover:bg-red-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
            )
            .on("click", function () {
              const index = selectedFuncImageFiles.indexOf(file);
              if (index > -1) {
                selectedFuncImageFiles.splice(index, 1);
              }
              listItem.remove();
            });

          listItem.append(fileName, removeButton);
          selectedFuncImageList.append(listItem);
        }
        // Clear the input value to allow selecting the same file again
        $(this).val("");
      }
    });

    // Handling selected additional images
    const imageDetailsInput = $("#image_details")[0];
    const selectedImagesList = $("#selected_images_list");

    $(imageDetailsInput).on("change", function () {
      const files = this.files;
      if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
          const file = files[i];
          selectedImageDetailsFiles.push(file);

          const listItem = $("<li>").addClass(
            "flex items-center justify-between py-2 px-3 rounded-md bg-gray-100 border border-gray-200"
          );

          const fileName = $("<span>").text(file.name);

          const removeButton = $("<button>")
            .text("ลบ")
            .addClass(
              "ml-4 px-3 py-1 bg-red-500 hover:bg-red-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
            )
            .on("click", function () {
              const index = selectedImageDetailsFiles.indexOf(file);
              if (index > -1) {
                selectedImageDetailsFiles.splice(index, 1);
              }
              listItem.remove();
            });

          listItem.append(fileName, removeButton);
          selectedImagesList.append(listItem);
        }
        // Clear the input value to allow selecting the same file again
        $(this).val("");
      }
    });
  }

  /**
   * Validates form input and collects form data if valid
   * @returns {FormData|boolean} FormData object if valid, false otherwise
   */
  let selectedImageDetailsFiles = [];

  function validateAndCollectFormData() {
    const product_name = $("#product_name").val().trim();
    const item_number = $("#item_number").val().trim();
    const status = $("#status").val();
    const imageFile = $("#image")[0].files[0];
    const content_en = $("#content_en").summernote("code").trim();
    const content_th = $("#content_th").summernote("code").trim();

    const car_model_input = $("#car_model_input").val().trim();
    const car_brand_input = $("#car_brand_input").val().trim();

    // Get radio values
    const latest_release =
      $('input[name="latest_release"]:checked').val() || "";
    const branch = $('input[name="branch"]:checked').val() || "";

    const category = window.getSelectedCategory ? getSelectedCategory() : "";
    const category_detail = window.getSelectedCategoryDetail
      ? getSelectedCategoryDetail()
      : "";

    

    const formData = new FormData();
    formData.append("action", "add_product");
    formData.append("product_name", product_name);
    formData.append("item_number", item_number);
    formData.append("status", status);
    formData.append("category", category);
    formData.append("category_detail", category_detail);
    formData.append("Product_content_en", content_en);
    formData.append("Product_content_th", content_th);
    formData.append("latest_release", latest_release);
    formData.append("branch", branch);
    formData.append("car_model_input", car_model_input);
    formData.append("car_brand_input", car_brand_input);

    if (imageFile) {
      formData.append("image", imageFile);
    }

    // ✅ Product Function Images
    for (let i = 0; i < selectedFuncImageFiles.length; i++) {
      formData.append("product_func_image[]", selectedFuncImageFiles[i]);
    }

    // ✅ Car Images
    const car_image_upload = $("#car_image_upload")[0].files;
    for (let i = 0; i < car_image_upload.length; i++) {
      formData.append("car_image_upload", car_image_upload[i]);
    }

    const car_image_upload_brand = $("#car_image_upload_brand")[0].files;
    for (let i = 0; i < car_image_upload_brand.length; i++) {
      formData.append("car_image_upload_brand", car_image_upload_brand[i]);
    }

    // ✅ Detail Images
    for (let i = 0; i < selectedImageDetailsFiles.length; i++) {
      formData.append("image_details[]", selectedImageDetailsFiles[i]);
    }

    // ✅ Manual PDF
    const manualPDF = $("#manual_pdf_input")[0]?.files[0];
    if (manualPDF) {
      formData.append("manual_pdf", manualPDF);
    }
    const ProductSheetPDF = $("#ProductSheet_pdf_input")[0]?.files[0];
    if (ProductSheetPDF) {
      formData.append("ProductSheet_pdf", ProductSheetPDF);
    }

    // ✅ YouTube Links
    $('input[name="youtube_links[]"]').each(function () {
      const link = $(this).val().trim();
      if (link) {
        formData.append("youtube_links[]", link);
      }
    });

    return formData;
  }

  function handleFormSubmission(result) {
    if (result.isConfirmed) {
      $.ajax({
        url: "../back-php/order_manage.php",
        method: "POST",
        data: result.value,
        contentType: false,
        processData: false,
        success: function (response) {
          Swal.fire("Success!", "Product has been added.", "success").then(
            () => {
              // รีเฟรชหน้าเมื่อผู้ใช้กด OK
              location.reload();
            }
          );
          selectedImageDetailsFiles = [];
          $("#selected_images_list").empty();
        },
        error: function () {
          Swal.fire("Error", "Failed to add product.", "error");
        },
      });
    } else {
      selectedImageDetailsFiles = [];
      $("#selected_images_list").empty();
    }
  }

  function openAddProductModal() {
    Swal.fire({
      title: "ยืนยันการเพิ่มสินค้า?",
      showCancelButton: true,
      confirmButtonText: "ยืนยัน",
      cancelButtonText: "ยกเลิก",
      preConfirm: () => {
        const formData = validateAndCollectFormData();
        if (!formData) return false;
        return formData;
      },
    }).then(handleFormSubmission);
  }

  /**
   * Sets up event listeners for form elements
   */
  function setupFormEventListeners() {
    let categoryMode = "select";
    let categoryDetailMode = "select";

    const hideElements = (...selectors) =>
      selectors.forEach((sel) => $(sel).addClass("hidden").hide());
    const showElements = (...selectors) =>
      selectors.forEach((sel) => $(sel).removeClass("hidden").show());

    // ซ่อน input/select ทุกตัวตอนเริ่มต้น
    hideElements(
      "#category_select_element",
      "#category_input_element",
      "#car_brand_input",
      "#car_brand_input_label",
      "#upload_section_brand",
      "#car_model_input",
      "#car_model_input_label",
      "#upload_section",
      "#category_detail_select_element",
      "#category_detail_input_element",
      "#Latest_Release"
    );

    // toggle Latest Release
    $('input[name="latest_release"]').on("change", function () {
      const isLatest =
        $('input[name="latest_release"]:checked').val() === "latest_release";
      isLatest
        ? showElements("#Latest_Release")
        : hideElements("#Latest_Release");
    });

    // toggle category input/select
    $("#category_select").on("click", function () {
      categoryMode = "select";
      showElements("#category_select_element");
      hideElements(
        "#category_input_element",
        "#car_model_input",
        "#car_model_input_label",
        "#upload_section"
      );

      // ตรวจสอบค่าที่เลือกเพื่อแสดง/ซ่อนฟิลด์ต่อไป
      handleCategoryVisibility($("#category_select_element").val());
    });

    $("#category_input").on("click", function () {
      categoryMode = "input";
      showElements("#category_input_element");
      hideElements(
        "#category_select_element",
        "#car_model_input",
        "#car_model_input_label",
        "#upload_section"
      );

      // ตรวจสอบค่าที่ป้อนเพื่อแสดง/ซ่อนฟิลด์ต่อไป
      handleCategoryVisibility($("#category_input_element").val());
    });

    // toggle category detail input/select
    $("#category_detail_select").on("click", function () {
      categoryDetailMode = "select";
      showElements("#category_detail_select_element");
      hideElements("#category_detail_input_element");
    });

    $("#category_detail_input").on("click", function () {
      categoryDetailMode = "input";
      showElements("#category_detail_input_element");
      hideElements("#category_detail_select_element");
    });

    function handleCategoryVisibility(categoryValue) {
      const val = categoryValue.trim().toLowerCase();
      const isSpecialCategory = [
        "diamond replacement parts  pickup, car & truck",
        "fitt vehicle styling accessories",
        "diamond replacement parts  motorcycle",
      ].includes(val);

      if (isSpecialCategory) {
        showElements(
          "#car_brand_input",
          "#car_brand_input_label",
          "#upload_section_brand",
          "#car_model_input",
          "#car_model_input_label",
          "#upload_section"
        );
        hideElements(
          "#category_detail_label",
          "#category_detail_select",
          "#category_detail_input",
          "#category_detail_select_element",
          "#category_detail_input_element"
        );
      } else {
        hideElements(
          "#car_brand_input",
          "#car_brand_input_label",
          "#upload_section_brand",
          "#car_model_input",
          "#car_model_input_label",
          "#upload_section"
        );
        showElements(
          "#category_detail_label",
          "#category_detail_select",
          "#category_detail_input"
        );
        categoryDetailMode === "select"
          ? showElements("#category_detail_select_element")
          : showElements("#category_detail_input_element");
      }
    }

    // เมื่อ category เปลี่ยน
    $("#category_select_element").on("change", function () {
      handleCategoryVisibility($(this).val());
    });

    $("#category_input_element").on("input", function () {
      handleCategoryVisibility($(this).val());
    });

    // Export getter functions ให้สามารถเรียกจากนอก scope นี้ได้
    window.getSelectedCategory = () =>
      categoryMode === "select"
        ? $("#category_select_element").val().trim()
        : $("#category_input_element").val().trim();

    window.getSelectedCategoryDetail = () =>
      categoryDetailMode === "select"
        ? $("#category_detail_select_element").val().trim()
        : $("#category_detail_input_element").val().trim();
  }

  $(document).ready(function () {
    setupFormEventListeners();
  });

  // โหลดข้อมูลเมื่อหน้าเว็บโหลด
  loadOrders();

  // ค้นหาสินค้า
  $("#searchBtn").click(function () {
    let searchTerm = $("#searchInput").val().trim();
    loadOrders(searchTerm, currentSortOrder);
  });

  // เรียงลำดับล่าสุด
  $("#sortLatest").click(function () {
    currentSortOrder = "DESC";
    loadOrders($("#searchInput").val().trim(), currentSortOrder);
  });

  // เรียงลำดับเก่าสุด
  $("#sortOldest").click(function () {
    currentSortOrder = "ASC";
    loadOrders($("#searchInput").val().trim(), currentSortOrder);
  });

  $(document).on("click", ".delete-product", function (e) {
    e.preventDefault();
    const productId = $(this).data("product_id");

    Swal.fire({
      title: "คุณแน่ใจหรือไม่?",
      text: "คุณต้องการลบสินค้านี้หรือไม่? การลบจะไม่สามารถย้อนกลับได้",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "ใช่, ลบเลย!",
      cancelButtonText: "ยกเลิก",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "../back-php/order_manage.php",
          type: "POST",
          data: {
            action: "delete_product",
            product_id: productId,
          },
          success: function (res) {
            let response = JSON.parse(res);
            if (response.success) {
              Swal.fire("ลบแล้ว!", response.message, "success").then(() => {
                location.reload(); // รีเฟรชหน้าเมื่อผู้ใช้กด OK
              });
            } else {
              Swal.fire("เกิดข้อผิดพลาด!", response.error, "error");
            }
          },
          error: function () {
            Swal.fire("ล้มเหลว!", "ไม่สามารถติดต่อเซิร์ฟเวอร์ได้", "error");
          },
        });
      }
    });
  });
});


$(document).ready(function () {
    let allProductData = [];

    $(document).on("click", "#openModal-addimgcategory-detail", function () {
        $.ajax({
            url: "../back-php/order_manage.php?action=fetch_orders",
            method: "GET",
            dataType: "json",
            success: function (response) {
                if (response && Array.isArray(response)) {
                    allProductData = response;

                    const excludedCategories = [
                        'DIAMOND Replacement Parts  Motorcycle',
                        'DIAMOND Replacement Parts  Pickup, Car & Truck',
                        'FITT Vehicle Styling Accessories'
                    ];

                    const uniqueCategories = [
                        ...new Set(
                            response
                                .map((p) => p.category)
                                .filter((c) => c && c.trim() !== "" && !excludedCategories.includes(c))
                        ),
                    ];
                    const uniqueCategoryDetails = [
                        ...new Set(
                            response
                                .map((p) => p.category_detail)
                                .filter((d) => d && d.trim() !== "")
                        ),
                    ];

                    let categoryOptions = '<option value="">Select Category</option>';
                    uniqueCategories.forEach((category) => {
                        categoryOptions += `<option value="${category}">${category}</option>`;
                    });

                    let categoryDetailOptions =
                        '<option value="">Select Category Detail</option>';
                    uniqueCategoryDetails.forEach((detail) => {
                        categoryDetailOptions += `<option value="${detail}">${detail}</option>`;
                    });

                    Swal.fire({
                        title: "เพิ่มรูปหมวดหมู่สินค้า",
                        html: createProductFormHTML(
                            categoryOptions,
                            categoryDetailOptions
                        ),
                        showCancelButton: true,
                        confirmButtonText: "เพิ่มรูปหมวดหมู่",
                        cancelButtonText: "ยกเลิก",
                        width: "90%",
                        preConfirm: validateAndCollectFormData,
                        didOpen: () => {
                            setupDynamicCategoryDetail(allProductData);
                            setupSingleImagePreview(); // Call the new image preview setup
                        },
                    }).then(handleFormSubmission);
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An error occurred while fetching data.",
                });
            },
        });
    });

    function createProductFormHTML(categoryOptions, categoryDetailOptions) {
        return `
            <div class="p-6 bg-gray-50 rounded-lg shadow-xl max-w-3xl mx-auto font-sans">
                <div class="flex flex-col md:flex-row justify-between items-center md:items-start gap-6 mb-8">
                    <div class="form-group w-full md:w-1/2">
                        <label for="category_select_element" class="block text-gray-700 text-sm font-semibold mb-2">Category</label>
                        <select id="category_select_element" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800 bg-white transition duration-150 ease-in-out">
                            ${categoryOptions}
                        </select>
                    </div>
                    <div class="form-group w-full md:w-1/2">
                        <label id="category_detail_label" for="category_detail_select_element" class="block text-gray-700 text-sm font-semibold mb-2" style="display: none;">Category Detail</label>
                        <select id="category_detail_select_element" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800 bg-white transition duration-150 ease-in-out" style="display: none;">
                            ${categoryDetailOptions}
                        </select>
                    </div>
                </div>

                <div class="form-group text-center mt-8 p-6 bg-white rounded-lg shadow-md border border-gray-200">
                    <label for="image_category_details" class="block text-gray-700 text-lg font-semibold mb-4">Select Image</label>
                    <input type="file" id="image_category_details" accept="image/*" class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100 cursor-pointer
                        focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <div id="image_preview_container" class="mt-4 flex justify-center items-center h-48 bg-gray-100 rounded-md overflow-hidden border border-dashed border-gray-300">
                        <img id="selected_image_preview" src="" alt="Image Preview" class="max-w-full max-h-full object-contain hidden">

                    </div>
                </div>
            </div>
        `;
    }

    function setupDynamicCategoryDetail(productsData) {
        const categorySelect = document.getElementById("category_select_element");
        const categoryDetailSelect = document.getElementById(
            "category_detail_select_element"
        );
        const categoryDetailLabel = document.getElementById(
            "category_detail_label"
        );

        categoryDetailSelect.style.display = "none";
        categoryDetailLabel.style.display = "none";

        categorySelect.addEventListener("change", function () {
            const selectedCategory = this.value;

            if (selectedCategory) {
                const filteredDetails = productsData
                    .filter(
                        (p) =>
                            p.category === selectedCategory &&
                            p.category_detail &&
                            p.category_detail.trim() !== ""
                    )
                    .map((p) => p.category_detail);

                const uniqueFilteredDetails = [...new Set(filteredDetails)];

                categoryDetailSelect.innerHTML =
                    '<option value="">Select Category Detail</option>';

                uniqueFilteredDetails.forEach((detail) => {
                    categoryDetailSelect.innerHTML += `<option value="${detail}">${detail}</option>`;
                });

                categoryDetailSelect.style.display = "block";
                categoryDetailLabel.style.display = "block";
            } else {
                categoryDetailSelect.innerHTML =
                    '<option value="">Select Category Detail</option>';
                categoryDetailSelect.style.display = "none";
                categoryDetailLabel.style.display = "none";
            }
        });
    }

    function setupSingleImagePreview() {
        const imageInput = document.getElementById('image_category_details');
        const imagePreview = document.getElementById('selected_image_preview');
        const noImageText = document.getElementById('no_image_selected_text');

        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0]; // Get the first selected file

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('hidden'); // Show the image
                    noImageText.classList.add('hidden'); // Hide the text
                };
                reader.readAsDataURL(file); // Read the file as a Data URL
            } else {
                imagePreview.src = ''; // Clear image source
                imagePreview.classList.add('hidden'); // Hide the image
                noImageText.classList.remove('hidden'); // Show the text
            }
        });
    }

function validateAndCollectFormData() {
        // Corrected IDs
        const category = $("#category_select_element").val().trim();
        const category_details = $("#category_detail_select_element").val().trim();
        const image_category_details_file = $("#image_category_details")[0].files[0]; // Get the file object

        // Corrected validation logic
        if (!category) {
            Swal.showValidationMessage("Please select a Category.");
            return false;
        }

        // Only validate category_details if the label is visible (meaning a category was selected)
        if ($("#category_detail_label").css('display') !== 'none' && !category_details) {
            Swal.showValidationMessage("Please select a Category Detail.");
            return false;
        }

        if (!image_category_details_file) {
            Swal.showValidationMessage("Please select an image.");
            return false;
        }

        const formData = new FormData();
        formData.append("action", "add_image_category_details");
        formData.append("category", category);
        formData.append("category_details", category_details);
        formData.append("image_category_details", image_category_details_file); // Append the file object

        return formData;
    }

    function handleFormSubmission(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: "../back-php/order_manage.php",
                method: "POST",
                data: result.value, // result.value already contains the FormData object
                contentType: false, // Important for FormData
                processData: false, // Important for FormData
                success: function (response) {
                    Swal.fire("Success!", "Image category details have been added.", "success").then(
                        () => {
                            location.reload(); // Refresh the page
                        }
                    );
                    // Resetting the form elements after successful submission
                    // This part might not be strictly necessary if you're reloading the page
                    // But useful if you decide not to reload.
                    $("#category_select_element").val("");
                    $("#category_detail_select_element").val("");
                    $("#image_category_details").val(""); // Clear the file input
                    $("#selected_image_preview").attr('src', '').addClass('hidden');
                    $("#no_image_selected_text").removeClass('hidden');
                    $("#category_detail_select_element").hide();
                    $("#category_detail_label").hide();
                },
                error: function (xhr, status, error) {
                    Swal.fire("Error", "Failed to add image category details.", "error");
                },
            });
        } else {
            // If the user cancels the dialog, reset the form elements
            $("#category_select_element").val("");
            $("#category_detail_select_element").val("");
            $("#image_category_details").val(""); // Clear the file input
            $("#selected_image_preview").attr('src', '').addClass('hidden');
            $("#no_image_selected_text").removeClass('hidden');
            $("#category_detail_select_element").hide();
            $("#category_detail_label").hide();
        }
    }
});




