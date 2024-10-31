window.addEventListener("DOMContentLoaded", (event) => {
  let root = document.documentElement;
  root.style.setProperty("--ncpc-font-size", 110);
  window.currentZoom = parseFloat(
    getComputedStyle(document.documentElement).getPropertyValue(
      "--ncpc-font-size"
    )
  );
});

(function ($) {
  "use strict";
  $(window).on("load", function () {
    var multipliers = 0;
    var normals = 0;
    var prevColor = {};
    var currentPrincing = {};
    var currentSize = ncpcData.currentConfig.pricingMode == "simple" ? 0 : {};
    window.ncpc_total_price = 0;
    var pricingCost = 0;
    var user_text = $("#ncpc-text-area").val() ?? "";

    editor_initialise();

    $("body").on("click", function (e) {
      changePrice();
    });

    //text field color fields
    $("#ncpc-text-area").on("input", function (e) {
      user_text = e.target.value;

      if (ncpcData.currentConfig.pricingMode == "simple") {
        if ($("input[name='ncpc-sizes']:checked").val() != undefined) {
          currentSize = parseFloat($("input[name='ncpc-sizes']:checked").val());
        } else {
          currentSize = parseFloat($("input[name='ncpc-sizes']").val());
        }
        applySimplePricing(user_text);
      }
    });
    $("#ncpc-text-area").on("change", function (e) {
      user_text = e.target.value;

      if (ncpcData.currentConfig.pricingMode == "simple") {
        if ($("input[name='ncpc-sizes']:checked").val() != undefined) {
          currentSize = parseFloat($("input[name='ncpc-sizes']:checked").val());
        } else {
          currentSize = parseFloat($("input[name='ncpc-sizes']").val());
        }
        applySimplePricing(user_text);
      }
    });

    //neon color fields
    $("input[name='ncpc-colors']").on("change", function (e) {
      if (prevColor.type == "multiplier") {
        multipliers -= prevColor.value;
      } else if (prevColor.type == "base") {
        normals -= prevColor.value;
      }

      if ($(this).attr("price-type") == "multiplier") {
        multipliers += parseFloat($(this).attr("price-value"));
      } else if ($(this).attr("price-type") == "base") {
        normals += parseFloat($(this).attr("price-value"));
      }
      prevColor = {
        type: $(this).attr("price-type"),
        value: parseFloat($(this).attr("price-value")),
      };

      changePrice();
    });

    $("input[name='ncpc-sizes']").on("change", function () {
      currentSize = parseInt($(this).val());
      applySimplePricing(user_text);
    });

    //fonts fields
    $("input[name='ncpc-fonts']").on("change", function () {
      currentPrincing =
        $(this).attr("pricing") != ""
          ? ncpcData.currentConfig.data.requiredOptions.priceOptions[
              parseInt($(this).attr("pricing"))
            ]
          : {};

      if (user_text != "") {
        if ($("input[name='ncpc-sizes']:checked").val() != undefined) {
          currentSize = parseFloat($("input[name='ncpc-sizes']:checked").val());
        } else {
          currentSize = parseFloat($("input[name='ncpc-sizes']").val());
        }
        applySimplePricing(user_text);
      }
    });

    //fonction that changes the price
    function changePrice() {
      window.ncpc_total_price = parseFloat(
        ncpcData.regularPrice.toString().replace(",", ".")
      );
      if (user_text != "") {
        window.ncpc_total_price += normals;
        window.ncpc_total_price += pricingCost;
        window.ncpc_total_price +=
          (multipliers * window.ncpc_total_price) / 100;
        $(".ncpc_total_price").html(formatPrice(window.ncpc_total_price));
      } else {
        window.ncpc_total_price = parseFloat(window.ncpc_total_price).toFixed(
          3
        );
        $(".ncpc_total_price").html(formatPrice(window.ncpc_total_price));
      }
    }

    function applySimplePricing(textContent) {
      pricingCost = 0;
      var eachLineText = textContent.split("\n");
      if (currentPrincing.prices[currentSize]) {
        currentPrincing.prices[currentSize].forEach((element, index) => {
          if (eachLineText[index] != undefined) {
            pricingCost += element.basicPrice;
            pricingCost += element.letterPrice * eachLineText[index].length;
          }
        });
      }
      changePrice();
    }

    //fonction that initialise all variables
    function editor_initialise() {
      if ($("input[name='ncpc-colors']").attr("price-type") == "multiplier") {
        multipliers += parseFloat(
          $("input[name='ncpc-colors']").attr("price-value")
        );
      } else if ($("input[name='ncpc-colors']").attr("price-type") == "base") {
        normals += parseFloat(
          $("input[name='ncpc-colors']").attr("price-value")
        );
      }
      prevColor = {
        type: $("input[name='ncpc-colors']").attr("price-type"),
        value: parseFloat($("input[name='ncpc-colors']").attr("price-value")),
      };
      currentSize = parseInt($("input[name='ncpc-sizes']:checked").val());
      if (ncpcData.currentConfig.data) {
        currentPrincing =
          ncpcData.currentConfig.data.requiredOptions.fontOptions.fonts.length >
          0
            ? ncpcData.currentConfig.data.requiredOptions.priceOptions[
                ncpcData.currentConfig.data.requiredOptions.fontOptions.fonts[0]
                  .pricing
              ]
            : {};
        applySimplePricing(user_text);
      }
    }

    function formatPrice(price) {
      let formattedPrice = parseFloat(price).toFixed(ncpcData.decimals);

      switch (ncpcData.currency_pos) {
        case "left":
          formattedPrice = ncpcData.currencySymbol + formattedPrice;
          break;
        case "right":
          formattedPrice = formattedPrice + ncpcData.currencySymbol;
          break;
        case "left_space":
          formattedPrice = ncpcData.currencySymbol + " " + formattedPrice;
          break;
        case "right_space":
          formattedPrice = formattedPrice + " " + ncpcData.currencySymbol;
          break;
      }

      // Remplacez le séparateur décimal et des milliers
      formattedPrice = formattedPrice.replace(".", ncpcData.decimalSep);
      formattedPrice = formattedPrice.replace(
        /(\d)(?=(\d{3})+(?!\d))/g,
        "$1" + ncpcData.thousandSep
      );

      return formattedPrice;
    }
  });
})(jQuery);
