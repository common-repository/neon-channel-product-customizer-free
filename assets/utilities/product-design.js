(function ($) {
  "use strict";
  $(function () {
    /* $(".ncpc-qty").keypress(function (e) {
			if (e.which < 48 || e.which > 57) {
				return(false);
			}
		}); */
    var ncpc_gotopro_notice = "ncpc_gotopro_notice"; // Clé pour le stockage local

    if (localStorage.getItem(ncpc_gotopro_notice) === "true") {
      $("#ncpc_gotopro_notice").hide(); // Cacher la notification si déjà fermée
    }
    var ncpc_config_page_notice = "ncpc_config_page_notice"; // Clé pour le stockage local

    if (localStorage.getItem(ncpc_config_page_notice) === "true") {
      $("#ncpc_config_page_notice").hide(); // Cacher la notification si déjà fermée
    }

    var ncpc_woo_notice = "ncpc_woo_notice"; // Clé pour le stockage local

    if (localStorage.getItem(ncpc_woo_notice) === "true") {
      $("#ncpc_woo_notice").hide(); // Cacher la notification si déjà fermée
    }

    var ncpc_permalink_notice = "ncpc_permalink_notice"; // Clé pour le stockage local

    if (localStorage.getItem(ncpc_permalink_notice) === "true") {
      $("#ncpc_permalink_notice").hide(); // Cacher la notification si déjà fermée
    }

    // Gérer le clic sur le bouton de fermeture
    $(document).on("click", ".ncpc-notice-nux .notice-dismiss", function () {
      let noticeKey = $(this).parent().attr("id");
      // Enregistrer dans le stockage local
      localStorage.setItem(noticeKey, "true");
    });

    $(".single_variation_wrap").on(
      "show_variation",
      function (event, variation) {
        // Fired when the user selects all the required dropdowns / attributes
        // and a final variation is selected / shown
        var variation_id = $("input[name='variation_id']").val();

        if (variation_id) {
          $(".ncpc-buttons-wrap-variation").hide();
          $(
            ".ncpc-buttons-wrap-variation[data-id='" + variation_id + "']"
          ).show();

          if (typeof hide_cart_button !== "undefined") {
            if (
              $(".ncpc-buttons-wrap-variation[data-id='" + variation_id + "']")
                .length > 0 &&
              hide_cart_button === true
            ) {
              $(".ncpc-buttons-wrap-variation")
                .parent()
                .find(".add_to_cart_button")
                .hide();
              $(".ncpc-buttons-wrap-variation")
                .parent()
                .find(".single_add_to_cart_button")
                .hide();
            } else {
              $(".ncpc-buttons-wrap-variation")
                .parent()
                .find(".add_to_cart_button")
                .show();
              $(".ncpc-buttons-wrap-variation")
                .parent()
                .find(".single_add_to_cart_button")
                .show();
            }
          }
          $(
            ".single-product .summary.entry-summary > .ncpc-buttons-wrap-variation"
          ).hide();
        }
      }
    );

    $(".single_variation_wrap").on(
      "hide_variation",
      function (event, variation) {
        $(".ncpc-buttons-wrap-variation").hide();
      }
    );

    $(document).on("click", ".ncpc_admin_download_image", function (e) {
      e.preventDefault();
      var imgageData = $(this).attr("href");
      if (imgageData.search("data") == 1) {
        var preview_img = dataToBlob(imgageData).blob;
      } else {
        var preview_img = imgageData;
      }
      var downloadLink = document.createElement("a");
      downloadLink.href = preview_img;
      const lowest = 100;
      const highest = 9999999;

      let randomNumber = parseInt(Math.random() * (highest - lowest) + lowest);
      downloadLink.download = "ncpc_preview_" + randomNumber + ".png";
      document.body.appendChild(downloadLink);
      downloadLink.click();
      document.body.removeChild(downloadLink);
    });

    function dataToBlob(dataURI) {
      var get_URL = function () {
        return window.URL || window.webkitURL || window;
      };

      var byteString = atob(dataURI.split(",")[1]),
        mimeString = dataURI.split(",")[0].split(":")[1].split(";")[0],
        arrayBuffer = new ArrayBuffer(byteString.length),
        _ia = new Uint8Array(arrayBuffer);

      for (var i = 0; i < byteString.length; i++) {
        _ia[i] = byteString.charCodeAt(i);
      }

      var dataView = new DataView(arrayBuffer);
      var blob = new Blob([dataView], { type: mimeString });
      return { blob: get_URL().createObjectURL(blob), data: dataURI };
    }

    var cartForm = $(".woocommerce-cart-form");
    cartForm.contents().each(function () {
      // Vérifier si le nœud est un nœud de texte
      if (this.nodeType === Node.TEXT_NODE) {
        // Modifier le contenu du nœud de texte
        this.textContent = "";
      }
    });
  });
})(jQuery);
