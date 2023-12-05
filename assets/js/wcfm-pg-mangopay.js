(function ($) {
  "use strict";

  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  let app = {
    init: function () {
      this.setup();
      this.bindEvents();
      this.showHideRelevantFields();
      this.showHideMpStatusFileds();
    },

    setup: function () {
      this.document = $(document);
      this.bank_type = ".mangopay-type";
      this.dependent_class = ".bank-type";
      this.user_mp_status = "#mangopay_user_mp_status";
      this.user_business_type = "#mangopay_user_business_type";
      this.mangopay_billing_country = "#mangopay_billing_country";
      this.submit_mp = "#submit_mp";
      this.mangopay_section_menu_item =
        "#mangopay_wrapper_section_options ul li a";
      this.mangopay_update_btn = "#update_business_information";
    },

    bindEvents: function () {
      this.document.on(
        "change",
        this.bank_type,
        this.showHideRelevantFields.bind(this)
      );
      this.document.on(
        "change",
        this.user_mp_status,
        this.showHideMpStatusFileds.bind(this)
      );
      this.document.on(
        "change",
        this.mangopay_billing_country,
        function (event) {
          const countrySelect = $("#mangopay_billing_country");
          const stateSelect = $("#mangopay_billing_state");
          // console.log("wecoder_mg_settings", wecoder_mg_settings);
          stateSelect.empty();
          app.changeBillingCountry(event, countrySelect, stateSelect);
        }
      );
      this.document.on(
        "click",
        this.submit_mp,
        this.createMangopayAcount.bind(this)
      );
      this.document.on(
        "click",
        this.mangopay_section_menu_item,
        this.changeMangopaySection
      );
      this.document.on(
        "click",
        this.mangopay_update_btn,
        this.updateBusinessInformation.bind(this)
      );
    },

    createMangopayAcount: function (event) {
      event.preventDefault();
      console.log("clcik");

      var errorMessages = $("#mangopay_error_messages");
      errorMessages.empty();

      // console.log("payment_mode", $("#payment_mode").val());
      // console.log("vendor_id", $("#mangopay_vendor_id").val());
      // console.log("mp_first_name", $("#mangopay_firstname").val());
      // console.log("mp_last_name", $("#mangopay_lastname").val());
      // console.log("mp_birthday", $("#mangopay_birthday").val());
      // console.log("mp_nationality", $("#mangopay_nationality").val());
      // console.log("mp_billing_country", $("#mangopay_billing_country").val());
      // console.log("mp_billing_state", $("#mangopay_billing_state").val());

      console.log("user_mp_status", $("#mangopay_user_mp_status").val());

      console.log(
        "user_business_type",
        $("#mangopay_user_mp_status").val() != "individual"
          ? $("#mangopay_user_business_type").val()
          : ""
      );

      let $loader = $("#ajax_loader");
      let $updated = $("#mp_submit");
      $loader.show();

      const validationFields = [
        {
          fieldId: "#mangopay_firstname",
          errorMessage: "Please select your first name",
        },
        {
          fieldId: "#mangopay_lastname",
          errorMessage: "Please select your last name",
        },
        {
          fieldId: "#mangopay_birthday",
          errorMessage: "Please select your birthday",
        },
        {
          fieldId: "#mangopay_nationality",
          errorMessage: "Please select your nationality",
        },
        {
          fieldId: "#mangopay_billing_country",
          errorMessage:
            "Please enter your legal representative country of residence",
        },
        // Add more fields as needed
      ];

      let isValid = true;

      for (const field of validationFields) {
        const value = $(field.fieldId).val();

        isValid = app.validateField(
          value,
          field.fieldId,
          field.errorMessage,
          function () {
            // Additional logic to execute on successful validation for this field
          }
        );
        if (!isValid) {
          $loader.hide();
          break;
        }
      }

      console.log(isValid);

      if (isValid) {
        $.ajax({
          type: "POST",
          dataType: "json",
          url: woodmart_settings.ajaxurl,
          data: {
            action: "create_mp_account",
            payment_method: $("#payment_mode").val(),
            vendor_id: $("#mangopay_vendor_id").val(),
            first_name: $("#mangopay_firstname").val(),
            last_name: $("#mangopay_lastname").val(),
            user_birthday: $("#mangopay_birthday").val(),
            user_nationality: $("#mangopay_nationality").val(),
            billing_country: $("#mangopay_billing_country").val(),
            billing_state: $("#mangopay_billing_state").val(),
            user_mp_status: $("#mangopay_user_mp_status").val(),
            user_business_type:
              $("#mangopay_user_mp_status").val() != "individual"
                ? $("#mangopay_user_business_type").val()
                : "",
          },
          success: function (response) {
            console.log("submitt6ed", response);
            $updated
              .html(response.data)
              .css({ color: "green", padding: "10px 0px" })
              .show();

            setTimeout(function () {
              $updated.fadeOut(1000, function () {
                // Redirect the user after the message fades out
                //window.location.href = "/my-office/settings";
                window.location.href = "/store-manager/settings";
              });
            }, 5000);
          },
          error: function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.status === 400) {
              // Display individual error messages
              var errors = jqXHR.responseJSON;
              if (errors) {
                // for (var key in errors) {
                //   if (errors.hasOwnProperty(key)) {
                //     console.log(errors[key]);
                //   }
                // }
                $("#mangopay_error_messages")
                  .text("Your are cheating!")
                  .css({ color: "red" })
                  .show(); // Show the error message immediately

                // Hide the error message after 10 seconds
                setTimeout(function () {
                  $("#mangopay_error_messages").fadeOut(1000);
                }, 10000);

                $loader.hide();
              }
            } else {
              // Handle other errors
              console.log("error - ", jqXHR);
            }
          },
          complete: function () {
            $loader.hide();
            console.log("complete");
          },
        });
      } else {
        console.log("Validation failed");
      }
    },

    updateBusinessInformation: function (event) {
      event.preventDefault();
      let $loader = $("#ajax_loader");
      let $updated = $("#bi_updated");
      $loader.show();

      /**
       * vendor id
       * Birthday
       * Nationality
       * Legal Representative Country Of Residence
       * Legal Representative Email
       * Company Number
       */

      // Business Information
      console.log("vendor_id", $("#mangopay_vendor_id").val());
      console.log("user_birthday", $("#mangopay_birthday").val());
      console.log("user_nationality", $("#mangopay_nationality").val());
      console.log("billing_country", $("#mangopay_billing_country").val());
      console.log("legal_email", $("#mangopay_legal_email").val());
      console.log("compagny_number", $("#mangopay_compagny_number").val());

      // Headquartes Address
      console.log("headquarters_addressline1", $("#mangopay_hq_address").val());
      console.log(
        "headquarters_addressline2",
        $("#mangopay_hq_address2").val()
      );
      console.log("headquarters_city", $("#mangopay_hq_city").val());
      console.log("headquarters_region", $("#mangopay_hq_region").val());
      console.log(
        "headquarters_postalcode",
        $("#mangopay_hq_postalcode").val()
      );
      console.log("headquarters_country", $("#mangopay_hq_country").val());

      // terms accept
      console.log(
        "IsChecked",
        $("#mangopay_termsAndConditionsAccepted").prop("checked")
      );

      /**
       * Mangopay form validation
       */
      var isValid = true;

      const validationBusinessFields = [
        {
          fieldId: "#mangopay_birthday",
          errorMessage: "Please select your birthday",
        },
        {
          fieldId: "#mangopay_nationality",
          errorMessage: "Please select your nationality",
        },
        {
          fieldId: "#mangopay_billing_country",
          errorMessage: "Please select your billing country",
        },
        {
          fieldId: "#mangopay_legal_email",
          errorMessage: "Please enter your legal email",
        },
        {
          fieldId: "#mangopay_compagny_number",
          errorMessage: "Please enter compagny number",
        },
        {
          fieldId: "#mangopay_hq_address",
          errorMessage: "Please enter compagny number",
        },
        {
          fieldId: "#mangopay_hq_city",
          errorMessage: "Please enter compagny number",
        },
        {
          fieldId: "#mangopay_hq_region",
          errorMessage: "Please enter compagny number",
        },
        {
          fieldId: "#mangopay_hq_postalcode",
          errorMessage: "Please enter compagny number",
        },
        {
          fieldId: "#mangopay_hq_country",
          errorMessage: "Please enter compagny number",
        },
        // Add more fields as needed
      ];

      for (const field of validationBusinessFields) {
        const value = $(field.fieldId).val();

        isValid = app.validateField(
          value,
          field.fieldId,
          field.errorMessage,
          function () {
            // Additional logic to execute on successful validation for this field
          }
        );
        if (!isValid) {
          $loader.hide();
          break;
        }
      }

      if (isValid) {
        if ($("#mangopay_termsAndConditionsAccepted").prop("checked")) {
          console.log("ajax call");
          $("#_termsAndConditions").removeClass("wcfm_validation_failed");
          $("#mangopay_termsAndConditionsAccepted_error_msg")
            .find("#error-message")
            .text("");

          $.ajax({
            type: "POST",
            dataType: "json",
            url: woodmart_settings.ajaxurl,
            data: {
              action: "update_mp_business_information",
              vendor_id: $("#mangopay_vendor_id").val(),
              user_birthday: $("#mangopay_birthday").val(),
              user_nationality: $("#mangopay_nationality").val(),
              billing_country: $("#mangopay_billing_country").val(),
              legal_email: $("#mangopay_legal_email").val(),
              compagny_number: $("#mangopay_compagny_number").val(),
              headquarters_addressline1: $("#mangopay_hq_address").val(),
              headquarters_addressline2: $("#mangopay_hq_address2").val(),
              headquarters_city: $("#mangopay_hq_city").val(),
              headquarters_region: $("#mangopay_hq_region").val(),
              headquarters_postalcode: $("#mangopay_hq_postalcode").val(),
              headquarters_country: $("#mangopay_hq_country").val(),
              termsconditions: $("#mangopay_termsAndConditionsAccepted").prop(
                "checked"
              ),
            },
            success: function (response) {
              console.log("response", response);
              // if (response.success) {
              //   $updated
              //     .html("Successfully updated!")
              //     .css({ color: "green" })
              //     .show();
              //   setTimeout(function () {
              //     $updated.fadeOut(2000);
              //   }, 5000);
              // } else {
              //   $updated.html(response.msg).css({ color: "red" }).show();
              //   setTimeout(function () {
              //     $updated.fadeOut(2000);
              //   }, 5000);
              // }
            },
            complete: function () {
              $loader.hide();
            },
          });
        } else {
          $("#_termsAndConditions").addClass("wcfm_validation_failed");
          $("#mangopay_termsAndConditionsAccepted_error_msg")
            .find("#error-message")
            .text("Please agree terms conditions of Mangopay!")
            .css({ color: "red" });
          $loader.hide();
          isValid = false;
        }
      }
    },

    showHideRelevantFields: function (event) {
      let type;

      if (undefined == event) {
        if (!$(this.bank_type).length) return;
        type = $(this.bank_type).val();
      } else {
        type = $(event.currentTarget).val();
      }

      let dependent_field = {
        show: this.dependent_class + ".bank-type-" + type.toLowerCase(),
        hide:
          this.dependent_class + ":not(.bank-type-" + type.toLowerCase() + ")",
      };

      $(dependent_field.show).each(function (key, value) {
        $(value).show().prev("label").show().prev("p").show();
      });

      $(dependent_field.hide).each(function (key, value) {
        $(value).hide().prev("label").hide().prev("p").hide();
      });
    },

    showHideMpStatusFileds: function (event) {
      let user_type;

      if (undefined == event) {
        if (!$(this.user_mp_status).length) return;
        user_type = $(this.user_mp_status).val();
      } else {
        user_type = $(event.currentTarget).val();
      }

      if ("individual" === user_type) {
        $(this.user_business_type).hide().prev("label").hide().prev("p").hide();
      } else {
        $(this.user_business_type).show().prev("label").show().prev("p").show();
      }
    },

    changeBillingCountry: function (event, countrySelect, stateSelect) {
      event.preventDefault();

      // selected country
      const selectedCountry = countrySelect.val();

      // gettting all states
      const states = wecoder_mg_settings.states;

      // Clear the current state options
      //  $stateSelect.empty();

      // Get the states for the selected country
      var countryStates = states[selectedCountry];

      // Add the states as options to the state select element
      if (countryStates && Object.keys(countryStates).length > 0) {
        $(".mangopay_billing_state").show();
        stateSelect.show();
        $.each(countryStates, function (stateCode, stateName) {
          //  console.log("in", stateCode, stateName);
          stateSelect.append(
            $("<option>", {
              value: stateCode,
              text: stateName,
            })
          );
        });
      } else {
        stateSelect.hide();
        $(".mangopay_billing_state").hide();
      }
    },

    validateField: function (value, elementId, errorMessage, callback = null) {
      const element = $(elementId);
      const errorContainer = element.next().find("#error-message");

      if (value !== "") {
        element.removeClass("wcfm_validation_failed");
        errorContainer.text("");
        if (callback) {
          callback();
        }
        return true; // Validation passed
      } else {
        element.addClass("wcfm_validation_failed");
        errorContainer.text(errorMessage).css({ color: "red" });
        return false; // Validation failed
      }
    },

    changeMangopaySection: function (event) {
      event.preventDefault();
      $("#mangopay_wrapper_section_options ul li a").removeClass("active");
      $(this).addClass("active");
      var link = $(this).attr("data-link");
      $(".mangopay_information_section").hide();
      $("#" + link).show();
    },
  };

  $(app.init.bind(app));
})(jQuery);
