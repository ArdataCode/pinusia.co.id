/**
 * Javascript Library for Layout Builder Admin
 *
 * - Page Layouts Model
 *
 * @author     AlpusTheme
 * @since      1.0
 * @package    Alpus Theme
 * @subpackage Theme
 */
"use strict";

window.themeAdmin = window.themeAdmin || {};

(function($) {
  /**
   * Layout Builder Model Class
   *
   * @since 1.0
   */
  var LayoutBuilderModel = {
    /**
     * Setup layout builder model.
     *
     * @since 1.0
     */
    init: function() {
      this.conditions =
        JSON.parse(JSON.stringify(alpha_layout_vars.conditions)) || {};
      this.schemes = alpha_layout_vars.schemes || {};
      this.clipboard = false;
      this.controls = [];
      for (var part in alpha_layout_vars.controls) {
        if (!part.startsWith("content")) {
          for (var key in alpha_layout_vars.controls[part]) {
            this.controls.push(key);
          }
        }
      }
    },

    /**
     * Get conditions by category or get all conditions.
     *
     * @since 1.0
     * @param {string} category
     * @param {number} conditionNo
     */
    getConditions: function(category = "", conditionNo = -1) {
      if (!category) {
        return this.conditions;
      }
      if (!this.conditions[category]) {
        this.conditions[category] = [];
      }
      if (conditionNo >= 0 && this.conditions[category][conditionNo]) {
        return this.conditions[category][conditionNo];
      }
      return this.conditions[category];
    },

    /**
     * Get layout option values by category.
     *
     * @since 1.0
     * @param {string} category
     * @param {number} conditionNo
     */
    getOptionValues: function(category, conditionNo) {
      return this.conditions[category] && this.conditions[category][conditionNo]
        ? this.conditions[category][conditionNo].options
        : false;
    },

    /**
     * Get condition title of given category.
     *
     * @since 1.0
     * @param {string} category
     * @param {boolean} getLayoutTitle
     */
    getConditionTitle: function(category, getLayoutTitle) {
      if (category && this.schemes[category]) {
        return getLayoutTitle
          ? this.schemes[category].layout_title
          : this.schemes[category].title;
      }
      return "";
    },

    /**
     * Set condition title
     * @param {string} category
     * @param {number} conditionNo
     * @param {string} title
     */
    setConditionTitle: function(category, conditionNo, title) {
      if (this.conditions[category][conditionNo]) {
        this.conditions[category][conditionNo].title = title;
      }
      this.requireSave();
    },

    /**
     * Get scheme by category.
     *
     * @since 1.0
     * @param {string} category
     * @param {string} type
     */
    getScheme: function(category, type = "") {
      return type
        ? this.schemes[category].scheme[type]
        : this.schemes[category].scheme;
    },

    /**
     * Get layout option controls for given layout part.
     *
     * @since 1.0
     * @param {string} part
     */
    getOptionControls: function(part) {
      return alpha_layout_vars.controls[part]
        ? alpha_layout_vars.controls[part]
        : false;
    },

    /**
     * Get templates by block type.
     *
     * @since 1.0
     * @param {string} block_type
     */
    getTemplates: function(block_type) {
      return alpha_layout_vars.templates[block_type];
    },

    /**
     * Check if new conditions could be added for given category.
     *
     * @since 1.0
     * @param {string} category Conditions category to check
     * @param {string} type Condition type to check.
     */
    canExtendCondition: function(category, type = "") {
      if (
        !category ||
        !this.schemes[category] ||
        !this.schemes[category].scheme
      ) {
        return false;
      }
      return (
        !type ||
        (this.schemes[category].scheme[type] &&
          (this.schemes[category].scheme[type].list ||
            this.schemes[category].scheme[type].ajaxselect))
      );
    },

    /**
     * Update condition UI.
     * @since 1.0
     * @param {string} category
     */
    updateCategoryUI: function(category = "") {
      var _updateCategoryUI = function(category) {
        // update UI
        var $count = $(
          ".alpha-condition-cat-" + category + "> .alpha-condition-count"
        );
        var count = this.conditions[category].filter(function(v) {
          return v;
        }).length;
        $count.text("(" + count + ")");
        count ? $count.slideDown() : $count.slideUp();
      }.bind(this);

      // update special category
      category && _updateCategoryUI(category);

      // count total
      var count = 0;
      for (var cat in this.conditions) {
        count += this.conditions[cat].filter(function(v) {
          return v;
        }).length;
        // update all categories
        category || _updateCategoryUI(cat);
      }
      $(".alpha-condition-cat-site > .alpha-condition-count")
        .text("(" + count + ")")
        .slideDown();
    },

    /**
     * Add a new empty condition.
     *
     * @since 1.0
     * @param {string} category
     * @param {string} type
     * @return {number} added index
     */
    addCondition: function(category) {
      if (!this.conditions[category]) {
        this.conditions[category] = [];
      }

      var data = {};
      data.title =
        this.getConditionTitle(category, true) +
        " " +
        (this.conditions[category].length + 1);
      data.scheme = {};
      if (this.schemes[category].scheme && this.schemes[category].scheme.all) {
        data.scheme.all = true;
      }
      this.conditions[category].push(data);

      this.updateCategoryUI(category);
      this.requireSave();

      // return added index
      return this.conditions[category].length - 1;
    },

    /**
     * Delete a condition.
     *
     * @since 1.0
     * @param {string} category
     * @param {number} conditionNo
     */
    deleteCondition: function(category, conditionNo) {
      if ("undefined" != typeof this.conditions[category][conditionNo]) {
        this.conditions[category].splice(conditionNo, 1);
        $(".alpha-layout-item[data-category=" + category + "]").each(
          function() {
            var no = this.getAttribute("data-condition-no");
            if (no > conditionNo) {
              this.setAttribute("data-condition-no", no - 1);
              $(this).data("condition-no", no - 1);
            }
          }
        );
        $("#alpha_layout_content")
          .isotope("updateSortData")
          .isotope();
      }
      this.updateCategoryUI(category);
      this.requireSave();
    },

    /**
     * Reset a condition. If no parameter is given, all options will be reset.
     *
     * @since 1.0
     * @param {string} category
     * @param {number} conditionNo
     */
    // resetCondition: function (category, conditionNo) {
    //     if (category) {
    //         this.conditions[category][conditionNo] =
    //             alpha_layout_vars.conditions[category] && alpha_layout_vars.conditions[category][conditionNo] ?
    //                 JSON.parse(JSON.stringify(alpha_layout_vars.conditions[category][conditionNo])) :
    //                 {};
    //     } else {
    //         this.conditions =
    //             alpha_layout_vars.conditions ?
    //                 JSON.parse(JSON.stringify(alpha_layout_vars.conditions)) :
    //                 {};
    //     }
    //     this.requireSave();
    // },

    /**
     * Duplicate a condition.
     *
     * @since 1.0
     * @param {string} category
     * @param {number} conditionNo
     */
    duplicateCondition: function(category, conditionNo) {
      if (
        category &&
        "number" == typeof conditionNo &&
        this.conditions[category][conditionNo]
      ) {
        var duplicated = JSON.parse(
          JSON.stringify(this.conditions[category][conditionNo])
        );

        $(".alpha-layout-item[data-category=" + category + "]").each(
          function() {
            var no = this.getAttribute("data-condition-no");
            if (no > conditionNo) {
              this.setAttribute("data-condition-no", no * 1 + 1);
              $(this).data("condition-no", no * 1 + 1);
            }
          }
        );

        this.conditions[category].splice(conditionNo, 0, duplicated);
        this.updateCategoryUI(category);
        this.requireSave();
        return conditionNo + 1;
      }
    },

    /**
     * Copy options
     *
     * @since 1.0
     * @param {string} category
     * @param {number} conditionNo
     */
    copyOptions: function(category, conditionNo) {
      this.clipboard = {
        category: category,
        options: this.getOptionValues(category, conditionNo)
      };
    },

    /**
     * Paste options
     *
     * @since 1.0
     * @param {string} category
     * @param {number} conditionNo
     * @param {jQuery} $item
     */
    pasteOptions: function(category, conditionNo, $item) {
      if (this.clipboard) {
        if (category == this.clipboard.category) {
          // paste all options
          if (this.conditions[category][conditionNo]) {
            this.conditions[category][
              conditionNo
            ].options = this.clipboard.options;
          } else {
            this.conditions[category][conditionNo] = {
              options: this.clipboard.options
            };
          }
        } else {
          if (this.conditions[category][conditionNo].options) {
            // remove current options except content
            for (var optionName in this.conditions[category][conditionNo]
              .options) {
              if (this.controls.indexOf(optionName)) {
                delete this.conditions[category][conditionNo].options[
                  optionName
                ];
              }
            }
          } else {
            this.conditions[category][conditionNo].options = {};
          }
          // paste copied options except content
          for (var optionName in this.clipboard.options) {
            if (this.controls.indexOf(optionName)) {
              this.conditions[category][conditionNo].options[
                optionName
              ] = this.clipboard.options[optionName];
            }
          }
        }

        LayoutBuilderView.refreshLayoutStatus($item);
        this.requireSave();
      }
    },

    /**
     * Notify that save is required.
     *
     * @since 1.0
     */
    requireSave: function() {
      $(".alpha-layouts-save").addClass("require-save");
      $(window).trigger("show_btn_header");
    },

    /**
     * Add a new condition with type or update existing condition's type.
     *
     * @since 1.0
     * @param {string} category
     * @param {number} conditionNo
     * @param {string} scheme
     * @param {mixed} value {boolean} isChecked or {array} list
     */
    setConditionScheme: function(category, conditionNo, scheme, value) {
      if ("undefined" == typeof this.conditions[category][conditionNo]) {
        // add
        var v = {};
        v[scheme] = value;
        v.all = true;
        this.conditions[category][conditionNo] = { scheme: v };
      } else if (this.conditions[category][conditionNo]) {
        // update
        if (!this.conditions[category][conditionNo].scheme) {
          var v = {};
          v[scheme] = value;
          this.conditions[category][conditionNo].scheme = v;
        }
        if (value) {
          this.conditions[category][conditionNo].scheme[scheme] = value;
        } else {
          delete this.conditions[category][conditionNo].scheme[scheme];
        }
      }
      this.requireSave();
    },

    /**
     * Set type and list for given condition.
     *
     * @since 1.0
     * @param {string} category
     * @param {number} conditionNo
     * @param {string} type
     * @param {array} list
     */
    setConditionList: function(category, conditionNo, type, list) {
      this.conditions[category][conditionNo] = list
        ? { type: type, list: list }
        : { type: type };
      this.requireSave();
    },

    /**
     * Set layout options for given condition.
     *
     * @since 1.0
     * @param {string} category
     * @param {number} conditionNo
     * @param {string} option
     * @param {mixed} value
     */
    setConditionOption: function(category, conditionNo, option, value) {
      if (!this.conditions[category][conditionNo].options) {
        this.conditions[category][conditionNo].options = {};
      }
      if (value) {
        this.conditions[category][conditionNo].options[option] = value;
      } else {
        delete this.conditions[category][conditionNo].options[option];
      }
      this.requireSave();
    },

    /**
     * Save all modifications of conditions.
     *
     * @since 1.0
     */
    save: function() {
      $(".alpha-layouts-save").removeClass("require-save");
      $(window).trigger("show_btn_header");

      if ( typeof window.top.alpha_core_vars.layout_save != 'undefined' ) {
          window.top.alpha_core_vars.layout_save = false;
      }

      $.post(
        alpha_layout_vars.ajax_url,
        {
          action: "alpha_layout_builder_save",
          nonce: alpha_layout_vars.nonce,
          conditions: this.conditions
        },
        function() {}
      ).fail(function() {
        $(".alpha-layouts-save").addClass("require-save");
        $(".alpha-modal-message").remove(); // issue : show message
        $(".alpha-layouts-save").before(
          '<span class="alpha-modal-message"></span>'
        );
        $(window).trigger("show_btn_header");
      });
    }
  };

  /**
   * Layout Builder View Class
   *
   * @since 1.0
   */
  var LayoutBuilderView = {
    /**
     * Setup layout builder view.
     *
     * @since 1.0
     */
    init: function() {
      // Delete button
      this.buttonDelete =
        '<button class="alpha-condition-button alpha-condition-delete far fa-trash-alt"></button>';
      // Duplicate button
      this.buttonDuplicate =
        '<button class="alpha-condition-button alpha-condition-duplicate far fa-clone"></button>';
      // Set conditions button
      this.buttonSet =
        '<button class="alpha-condition-button alpha-condition-set fa fa-cog"></button>';

      // get layout box template.
      this.layoutBoxTemplate = $("#alpha_layout_template").text();
      $("#alpha_layout_template").remove();

      // register events.
      $(document.body)
        // events for context menu
        .on("click", ".alpha-layouts-save", this.onSave)
        .on("contextmenu", ".alpha-layout-item", this.onContextMenu.bind(this))
        .on("click", "#alpha_layout_content", this.closeContextMenu)
        .on("click", ".alpha-condition-menu > a", this.clickContextMenuItem)
        .on("click", ".alpha-condition-copy", this.copyOptions)
        .on("click", ".alpha-condition-paste", this.pasteOptions)
        .on("click", ".alpha-condition-edit-back", this.goBackFromEdit)

        // events for condition
        .on("click", ".alpha-condition-cat", this.clickCategory.bind(this))
        .on("click", ".alpha-layout-more", this.addCondition.bind(this))
        .on("click", ".alpha-condition-delete", this.deleteCondition.bind(this))
        // .on('click', '.alpha-condition-reset', this.resetCondition.bind(this))
        // .on('click', '.alpha-layouts-reset', this.resetAll.bind(this))
        .on(
          "click",
          ".alpha-condition-duplicate",
          this.duplicateCondition.bind(this)
        )
        .on(
          "change",
          ".alpha-scheme-options > div > label input[type=checkbox]",
          this.changeConditionScheme.bind(this)
        )
        .on("change", ".alpha-scheme-list", this.changeConditionItem)

        // events for layout box
        .on(
          "input",
          ".alpha-condition-title",
          this.changeConditionTitle.bind(this)
        )
        .on("click", ".alpha-layout .layout-part", this.editPart)
        .on("click", ".alpha-condition-set", this.editCondition)
        .on("click", this.clickOther.bind(this))

        // events for layout control
        .on(
          "change",
          ".alpha-block-select input",
          this.changeBlockMode.bind(this)
        )
        .on(
          "change",
          ".alpha-layout-options input",
          this.changeOptionInput.bind(this)
        )
        .on(
          "change",
          ".alpha-layout-options select",
          this.changeOptionInput.bind(this)
        );

      this.setupLayouts();
    },

    /**
     * Initialize plugins for layout controls.
     *
     * @since 1.0
     */
    refreshUI: function(mode) {
      if (!mode || "layout" == mode || "add" == mode) {
        $("#alpha_layout_content").isotope();
      }
      if (!mode || "add" == mode) {
        this.refreshLayoutStatus();
      }
    },

    /**
     * Display status of layout parts.
     *
     * @since 1.0
     * @param {jQuery} $container This can be omitted.
     */
    refreshLayoutStatus: function($container) {
      $container || ($container = $("#alpha_layout_content"));
      $container.is(".alpha-layout-item") ||
        ($container = $container.find(".alpha-layout-item"));
      $container.each(function() {
        var $item = $(this);
        var category = $item.data("category");
        var conditionNo = $item.data("conditionNo");
        var optionValues = LayoutBuilderModel.getOptionValues(
          category,
          conditionNo
        );
        if (optionValues) {
          for (var part in alpha_layout_vars.controls) {
            if (LayoutBuilderModel.controls.indexOf(part)) {
              var optionControls = LayoutBuilderModel.getOptionControls(part);
              var $part = $item.find('.layout-part[data-part="' + part + '"]');
              var set = false;

              // Reset
              $part.removeClass("set hide");
              $part.children(".block-value").text("");

              // Check set
              for (var control in optionControls) {
                if (optionValues[control]) {
                  set = true;
                  break;
                }
              }

              if (optionControls[part] && "hide" == optionValues[part]) {
                // Hide
                $part.addClass("hide");
              } else if (set) {
                // Set
                $part.addClass("set");
                if (optionControls[part]) {
                  var blocks = LayoutBuilderModel.getTemplates(
                    optionControls[part].type.replace("block_", "")
                  );
                  if (blocks && blocks[optionValues[part]]) {
                    $part
                      .children(".block-value")
                      .text(blocks[optionValues[part]]);
                  }
                }
              }
            }
          }
        }
      });
    },

    /**
     * Setup layouts
     *
     * @since 1.0
     */
    setupLayouts: function() {
      var layoutItems = "";
      var schemes = LayoutBuilderModel.schemes;

      if (schemes) {
        for (var category in schemes) {
          // add layouts already set
          var layouts = LayoutBuilderModel.getConditions(category);
          for (var conditionNo in layouts) {
            layoutItems += this.getNewConditionUI(category, conditionNo);
          }

          // add more button
          if (
            "site" != category &&
            (LayoutBuilderModel.canExtendCondition(category) || !layouts.length)
          ) {
            layoutItems += this.getAddMoreUI(category);
          }
        }

        // show conditions
        $("#alpha_layout_content")
          .html(layoutItems)
          .isotope({
            layoutMode: "fitRows",
            filter: ".alpha-layout-item",
            sortBy: ["category", "no"],
            getSortData: {
              category: function(el) {
                var category = el.getAttribute("data-category");
                var categories = Object.keys(LayoutBuilderModel.schemes);
                return categories.indexOf(category);
              },
              no: function(el) {
                return parseInt(el.getAttribute("data-condition-no"));
              }
            }
          });

        // init plugins
        LayoutBuilderModel.updateCategoryUI();
        this.refreshUI();
      }
    },

    /**
     * Refresh conditions
     * @param {string} category
     * @param {number} conditionNo
     */
    refreshCondition: function(category, conditionNo) {
      var selector = ".alpha-layout-item";
      var view = this;

      category && (selector += '[data-category="' + category + '"]');
      conditionNo && (selector += '[data-condition-no="' + conditionNo + '"]');

      $(selector).each(function() {
        view.editPart({
          currentTarget: $(this)
            .find(".layout-part.active")
            .get(0)
        });
      });
    },

    /**
     * Event handler to save layout controls.
     *
     * @since 1.0
     */
    onSave: function() {
      LayoutBuilderModel.save();
    },

    /**
     * Event handler to show context menu.
     *
     * @since 1.0
     * @param {Event} e
     */
    onContextMenu: function(e) {
      this.closeContextMenu();

      var $item = $(e.currentTarget);
      var $container = $(".alpha-admin-panel-content");
      var containerOffset = $container.get(0).getBoundingClientRect();
      var category = $item.data("category");

      var html =
        '<div class="alpha-condition-menu" style="left:' +
        (e.clientX - containerOffset.x + $container.scrollLeft()) +
        "px;top:" +
        (e.clientY - containerOffset.y + $container.scrollTop()) +
        'px;">';
      var prefix = '<a href="#" class="alpha-condition-';

      html +=
        prefix +
        'copy"><i class="far fa-copy"></i>' +
        alpha_layout_vars.text_copy +
        "</a>";
      if (LayoutBuilderModel.clipboard) {
        html +=
          prefix +
          'paste"><i class="fa fa-paste"></i>' +
          alpha_layout_vars.text_paste +
          "</a>";
      }

      if (LayoutBuilderModel.canExtendCondition(category)) {
        html +=
          prefix +
          'duplicate"><i class="far fa-clone"></i>' +
          alpha_layout_vars.text_duplicate +
          "</a>";
      }

      html +=
        prefix +
        'set"><i class="fa fa-cog"></i>' +
        alpha_layout_vars.text_options +
        "</a>";

      html +=
        prefix +
        'delete"><i class="far fa-trash-alt"></i>' +
        alpha_layout_vars.text_delete +
        "</a>";

      html += "</div>";

      $container.append(html);
      $(".alpha-condition-menu").data("item", $item);
      e.preventDefault();
    },

    /**
     * Close context menu of condition.
     *
     * @since 1.0
     */
    closeContextMenu: function() {
      $(".alpha-condition-menu").remove();
    },

    /**
     * Event handler to show context menu for condition.
     *
     * @since 1.0
     * @param {Event} e
     */
    clickContextMenuItem: function(e) {
      e.preventDefault();
    },

    /**
     * Event handler to copy options.
     *
     * @since 1.0
     * @param {Event} e
     */
    copyOptions: function(e) {
      var $menuItem = $(e.currentTarget);
      var $item = $menuItem.parent().data("item");
      LayoutBuilderModel.copyOptions(
        $item.data("category"),
        $item.data("condition-no")
      );
    },

    /**
     * Event handler to paste options.
     *
     * @since 1.0
     * @param {Event} e
     */
    pasteOptions: function(e) {
      var $item = $(e.currentTarget)
        .parent()
        .data("item"); // from menu item.
      LayoutBuilderModel.pasteOptions(
        $item.data("category"),
        $item.data("condition-no"),
        $item
      );
    },

    /**
     * Event handler to show conditions by category.
     *
     * @since 1.0
     */
    clickCategory: function(e) {
      var $category = $(e.currentTarget).addClass("active");
      var category = $category.data("category");

      // toggle category
      $category.siblings(".active").removeClass("active");

      // filter layouts
      $("#alpha_layout_content").isotope({
        filter:
          "site" == category
            ? ".alpha-layout-item"
            : '[data-category="' + category + '"]'
      });
    },

    /**
     * Event handler to reset condition.
     *
     * @since 1.0
     */
    // resetCondition: function (e) {
    //     var $reset = $(e.currentTarget);
    //     var $item = $reset.is('.alpha-condition-menu > a') ?
    //         $reset.parent().data('item') :
    //         $reset.closest('.alpha-layout-item');

    //     LayoutBuilderModel.resetCondition($item.data('category'), $item.data('condition-no'));

    //     var $activePart = $item.find('.layout-part.active');
    //     $activePart.length &&
    //         this.editPart({
    //             currentTarget: $activePart.get(0)
    //         });
    // },

    /**
     * Event handler to reset all conditions.
     *
     * @since 1.0
     */
    // resetAll: function () {
    //     LayoutBuilderModel.resetCondition();
    //     $('.alpha-condition-cat-all').click();
    // },

    /**
     * Get add more item html.
     * @param {string} category
     */
    getAddMoreUI: function(category) {
      return (
        '<div class="alpha-layout-more-wrap" data-category="' +
        category +
        '" data-condition-no="999">' +
        '<div class="alpha-layout-more">' +
        '<div class="alpha-layout-more-inner"><i class="alpha-icon-plus"></i>' +
        alpha_layout_vars.text_create_layout +
        "<b>" +
        LayoutBuilderModel.getConditionTitle(category) +
        "</b>" +
        "</div></div></div>"
      );
    },

    /**
     * Get layout item html to add.
     * @param {string} category
     * @param {number} conditionNo
     */
    getNewConditionUI: function(category, conditionNo = -1) {
      // Create new
      if (conditionNo == -1) {
        conditionNo = LayoutBuilderModel.addCondition(category);
      }

      var layoutData = LayoutBuilderModel.getConditions(category, conditionNo);
      if (layoutData) {
        var conditionHtml = "";
        if (LayoutBuilderModel.canExtendCondition(category)) {
          var scheme = LayoutBuilderModel.getScheme(category);
          var schemeClass;
          var schemeData = layoutData.scheme || {};

          // condition heading
          conditionHtml +=
            '<div class="alpha-scheme-options"><label class="apply-text">' +
            alpha_layout_vars.text_apply_prefix +
            LayoutBuilderModel.getConditionTitle(category, true).toLowerCase() +
            alpha_layout_vars.text_apply_suffix +
            "</label>";

          // condition type
          for (var schemeKey in scheme) {
            schemeClass = "alpha-scheme-" + schemeKey;
            if (
              !(schemeData && schemeData.all && schemeKey == "all") &&
              !schemeData[schemeKey]
            ) {
              schemeClass += " disabled";
            }

            conditionHtml +=
              '<div class="' +
              schemeClass +
              '" data-scheme="' +
              schemeKey +
              '">';
            conditionHtml +=
              '<label><input type="checkbox"' +
              (schemeData[schemeKey] ? " checked" : "") +
              ">" +
              scheme[schemeKey].title +
              "</label>";

            var list = scheme[schemeKey].list;
            if (list) {
              conditionHtml +=
                '<select multiple class="alpha-scheme-list" data-placeholder="' +
                scheme[schemeKey].placeholder +
                '">';
              for (var item in list) {
                conditionHtml +=
                  '<option value="' +
                  item +
                  '"' +
                  (schemeData[schemeKey] &&
                  schemeData[schemeKey].length &&
                  (schemeData[schemeKey].indexOf(item) >= 0 ||
                    schemeData[schemeKey].indexOf(1 * item) >= 0)
                    ? " selected"
                    : "") +
                  ">" +
                  list[item] +
                  "</option>";
              }
              conditionHtml += "</select>";
            } else if (scheme[schemeKey].ajaxselect) {
              var option = "child" == schemeKey ? "page" : schemeKey;
              conditionHtml +=
                '<select multiple class="alpha-scheme-list ajaxselect2" data-placeholder="' +
                scheme[schemeKey].placeholder +
                '" data-load-option="' +
                option +
                '"' +
                (schemeData[schemeKey]
                  ? ' data-values="' +
                    (Array.isArray(schemeData[schemeKey])
                      ? schemeData[schemeKey].join(",")
                      : schemeData[schemeKey]) +
                    '"'
                  : "") +
                ">";
              conditionHtml += "</select>";
            }
            conditionHtml += "</div>";
          }

          // end condition
          conditionHtml += "</div>";
        }

        var isContentEmpty = !LayoutBuilderModel.getOptionControls(
          "content_" + category
        );

        this.layoutBoxTemplateReplaced = this.layoutBoxTemplate.replace(
          'class="layout-part content" data-part="content"',
          'class="layout-part content' +
            (isContentEmpty ? " disabled" : "") +
            '" data-part="content_' +
            category +
            '"'
        );

        if (category == "single_front") {
          this.layoutBoxTemplateReplaced = this.layoutBoxTemplate.replace(
            'class="layout-part ptb"',
            'class="layout-part ptb disabled"'
          );
        }

        this.layoutBoxTemplateReplaced = this.layoutBoxTemplateReplaced.replace(
            'class="layout-part top-sidebar sidebar"',
            'class="layout-part top-sidebar sidebar disabled"'
        );

        return (
          '<div class="alpha-layout-item alpha-layout-item-' +
          category +
          '" data-category="' +
          category +
          '" data-condition-no="' +
          conditionNo +
          '">' +
          // Layout header
          '<div class="alpha-condition">' +
          '<span class="alpha-condition-edit-back fa fa-arrow-left"></span>' +
          '<span class="alpha-condition-title" contenteditable="true">' +
          (layoutData.title
            ? layoutData.title
            : LayoutBuilderModel.getConditionTitle(category, true)) +
          "</span>" +
          (LayoutBuilderModel.canExtendCondition(category)
            ? this.buttonDuplicate + this.buttonSet
            : "") +
          ("site" == category ? "" : this.buttonDelete) +
          "</div>" +
          // Layout body
          '<div class="alpha-condition-layout">' +
          this.layoutBoxTemplateReplaced +
          '<div class="alpha-layout-options"><div></div></div>' +
          conditionHtml +
          "</div>" +
          "</div>"
        );
      }
    },

    /**
     * Add a new condition.
     *
     * @since 1.0
     */
    addCondition: function() {
      var totalCnt = 0;
      var conditions = LayoutBuilderModel.getConditions();
      for (var cat in conditions) {
        totalCnt += conditions[cat].filter(function(v) {
          return v;
        }).length;
      }

      if (!wp.hooks.applyFilters("alpha_add_layout_condition", totalCnt < 7)) {
        themeAdmin.prompt.showDialog({
          title: alpha_layout_vars.pro_title,
          content: alpha_layout_vars.pro_desc,
          actions: [
            {
              callback: function() {
                window.open(alpha_layout_vars.purchase_pro);
              }
            },
            { callback: "" }
          ]
        });
        return;
      }

      var category =
        $(".alpha-condition-cat.active").data("category") || "site";
      var addedUI = $(this.getNewConditionUI(category));

      // remove more
      LayoutBuilderModel.canExtendCondition(category) ||
        $('.alpha-layout-more-wrap[data-category="' + category + '"]').remove();

      // add new
      $("#alpha_layout_content")
        .append(addedUI)
        .isotope("appended", addedUI);
      this.refreshUI("add");
    },

    /**
     * Event handler to duplicate a condition.
     *
     * @since 1.0
     *
     * @param {Event} e
     */
    duplicateCondition: function(e) {
      var totalCnt = 0;
      var conditions = LayoutBuilderModel.getConditions();
      for (var cat in conditions) {
        totalCnt += conditions[cat].filter(function(v) {
          return v;
        }).length;
      }
      
      if (!wp.hooks.applyFilters("alpha_add_layout_condition", totalCnt < 7)) {
        themeAdmin.prompt.showDialog({
          title: alpha_layout_vars.pro_title,
          content: alpha_layout_vars.pro_desc,
          actions: [
            {
              callback: function() {
                window.open(alpha_layout_vars.purchase_pro);
              }
            },
            { callback: "" }
          ]
        });
        return;
      }

      var $duplicate = $(e.currentTarget);
      var $item;
      if ($duplicate.is(".alpha-condition-menu > a")) {
        $item = $duplicate.parent().data("item");
      } else {
        $item = $duplicate.closest(".alpha-layout-item");
      }

      var category = $item.data("category");
      var categoryNo = LayoutBuilderModel.duplicateCondition(
        category,
        $item.data("condition-no")
      );
      var addedUI = $(this.getNewConditionUI(category, categoryNo));

      // add duplicated
      $("#alpha_layout_content")
        .append(addedUI)
        .isotope("appended", addedUI)
        .isotope("updateSortData");
      this.refreshUI("add");
    },

    /**
     * Event handler to delete condition.
     *
     * @since 1.0
     */
    deleteCondition: function(e) {
      if (confirm(alpha_layout_vars.text_confirm_delete_condition)) {
        var $delete = $(e.currentTarget);
        var $item;
        if ($delete.is(".alpha-condition-menu > a")) {
          $item = $delete.parent().data("item"); // context menu item
        } else {
          $item = $delete.closest(".alpha-layout-item"); // or layout item's button
        }
        var category = $item.data("category");

        if ("site" != category) {
          // remove
          LayoutBuilderModel.deleteCondition(
            category,
            $item.data("condition-no")
          );
          $item.remove();

          // add more
          if (!LayoutBuilderModel.canExtendCondition(category)) {
            var $more = $(this.getAddMoreUI(category));
            $("#alpha_layout_content")
              .append($more)
              .isotope("appended", $more);
          }

          this.refreshUI("layout");
        }
      }
    },

    /**
     * Event handler to change condition type.
     *
     * @param {Event} e
     */
    changeConditionScheme: function(e) {
      var $check = $(e.currentTarget);
      var $scheme = $check.closest(".alpha-scheme-options>div");
      var scheme = $scheme.data("scheme");
      var $item = $scheme.closest(".alpha-layout-item");
      var category = $item.data("category");
      var conditionNo = $item.data("condition-no");
      var isChecked = $check.is(":checked");

      $scheme.toggleClass("disabled", !isChecked);
      LayoutBuilderModel.setConditionScheme(
        category,
        conditionNo,
        scheme,
        isChecked
      );

      // var $type = $(e.currentTarget);
      // var type = $type.val();
      // var $list = $type.next('.alpha-scheme-list');

      // if (type) {
      //     var schemeKey = LayoutBuilderModel.getScheme(category, type);
      //     var list = schemeKey.list;

      //     if (list) {
      //         var html = '';

      //         for (var item in list) {
      //             html += '<option value="' + item + '">' + list[item] + '</option>';
      //         }
      //         if ($list.length) {
      //             $list.data('select2') && $list.select2('destroy');
      //             $list.html(html).data('placeholder', schemeKey.placeholder);
      //         } else {
      //             $type.after('<select multiple class="alpha-scheme-list" data-placeholder="' + schemeKey.placeholder + '">' + html + '</select>');
      //         }

      //         this.refreshUI('condition');
      //     } else {
      //         $list.select2('destroy').remove();
      //     }
      // } else if ($list.length) {
      //     $list.select2('destroy').remove();
      // }
    },

    /**
     * Event handler to change condition item.
     *
     * @since 1.0
     *
     * @param {Event} e
     */
    changeConditionItem: function(e) {
      var $list = $(e.currentTarget);
      var $scheme = $list.closest(".alpha-scheme-options>div");
      var $item = $scheme.closest(".alpha-layout-item");
      var category = $item.data("category");
      var conditionNo = $item.data("condition-no");
      var scheme = $scheme.data("scheme");
      var list = $list.val();

      if (
        LayoutBuilderModel.canExtendCondition(category, scheme) &&
        typeof list == "object"
      ) {
        LayoutBuilderModel.setConditionScheme(
          category,
          conditionNo,
          scheme,
          list.length ? list : false
        );
      }
    },

    /**
     * Event handler to change condition title
     *
     * @since 1.0
     *
     * @param {Event} e
     */
    changeConditionTitle: function(e) {
      var $title = $(e.currentTarget);
      var $item = $title.closest(".alpha-layout-item");
      var category = $item.data("category");
      var conditionNo = $item.data("condition-no");
      LayoutBuilderModel.setConditionTitle(
        category,
        conditionNo,
        $title.text()
      );
    },

    /**
     * Event handler to show layout controls for clicked layout part.
     *
     * @since 1.0
     *
     * @param {Event} e
     */
    editPart: function(e) {
      var $part = $(e.currentTarget);

      // active part
      if ($part.hasClass("disabled")) {
        return;
      }

      var part = $part.data("part");
      var $layout = $part.closest(".alpha-layout");
      var $options = $layout.next(".alpha-layout-options").children();
      var controls_html = ""; // '<h4 class="alpha-layout-control">' + $part.text() + '</h4>';
      var $item = $part.closest(".alpha-layout-item");
      var currentCategory = $item.data("category");
      var conditionNo = $item.data("condition-no");

      // show layout options for selected layout part.
      var optionControls = LayoutBuilderModel.getOptionControls(part);
      var optionValues = LayoutBuilderModel.getOptionValues(
        currentCategory,
        conditionNo
      );

      if (optionControls) {
        var randomList = [];
        for (var optionName in optionControls) {
          // get random number.
          var random;
          do {
            random = Math.floor(Math.random() * 65535);
          } while (randomList.indexOf(random) >= 0);
          randomList.push(random);

          var name = "_alpha_" + part + "_" + optionName + random;
          var control = optionControls[optionName];
          var control_html = "";
          var optionValue =
            optionValues && "undefined" != typeof optionValues[optionName]
              ? optionValues[optionName]
              : "";

          // show label, description
          if (control.description) {
            control_html +=
              '<div class="alpha-layout-desc"><label>' +
              control.label +
              "</label><p>" +
              control.description +
              "</p></div>";
          } else {
            control_html +=
              '<label for="' +
              name +
              '" class="alpha-layout-desc">' +
              control.label +
              "</label>";
          }

          // show control
          if ("buttonset" == control.type) {
            var choice = "";

            control_html +=
              '<input type="radio" id="' +
              name +
              "_" +
              choice +
              '" name="' +
              name +
              '" value=""' +
              ("" == optionValue ? " checked" : "") +
              ' class="radio-default">';
            control_html +=
              '<label for="' +
              name +
              "_" +
              choice +
              '" class="label-default fas fa-redo"' +
              (optionValue ? "" : ' checked="true"') +
              "></label>";
            control_html += '<div class="alpha-radio-button-set">';
            for (var choice in control.options) {
              control_html +=
                '<input type="radio" id="' +
                name +
                "_" +
                choice +
                '" name="' +
                name +
                '" value="' +
                choice +
                '"' +
                (choice == optionValue ? " checked" : "") +
                ">"; // check checked
              control_html +=
                '<label for="' +
                name +
                "_" +
                choice +
                '" class="alpha_' +
                part +
                "_" +
                optionName +
                "_" +
                choice +
                '">' +
                control.options[choice] +
                "</label>";
              // control_html += '<img src="' + alpha_layout_vars.layout_images_url + control.options[choice].image + '" title="' + control.options[choice].title + '">';
            }
            control_html += "</div>";
          } else if ("image" == control.type) {
            var choice = "";
            control_html +=
              '<input type="radio" id="' +
              name +
              "_" +
              choice +
              '" name="' +
              name +
              '" value=""' +
              ("" == optionValue ? " checked" : "") +
              ' class="radio-default">';
            control_html +=
              '<label for="' +
              name +
              "_" +
              choice +
              '" class="label-default fas fa-redo"' +
              (optionValue ? "" : ' checked="true"') +
              "></label>";
            control_html += '<div class="alpha-radio-image-set">';
            for (var choice in control.options) {
              control_html +=
                '<input type="radio" id="' +
                name +
                "_" +
                choice +
                '" name="' +
                name +
                '" value="' +
                choice +
                '"' +
                (choice == optionValue ? " checked" : "") +
                ">"; // check checked
              control_html +=
                '<label for="' +
                name +
                "_" +
                choice +
                '" class="alpha_' +
                part +
                "_" +
                optionName +
                "_" +
                choice +
                '">';
              control_html +=
                '<img src="' +
                alpha_layout_vars.layout_images_url +
                control.options[choice].image +
                '" title="' +
                control.options[choice].title +
                '">';
              control_html += "</label>";
            }
            control_html += "</div>";
          } else if (control.type.startsWith("block")) {
            var blockType = control.type.replace("block_", "")
              ? control.type.replace("block_", "")
              : "block";
            var blocks = LayoutBuilderModel.getTemplates(blockType);

            control_html +=
              '<div class="alpha-block-select' +
              (optionValue && optionValue != "hide" ? "" : " inactive-my") +
              '">';

            control_html += '<div class="alpha-radio-button-set">';
            control_html +=
              '<input type="radio" name="' +
              name +
              '" id="' +
              name +
              '_" value=""' +
              (optionValue ? "" : " checked") +
              ">";
            control_html +=
              '<label class="fa fa-redo" for="' +
              name +
              '_" title="' +
              alpha_layout_vars.text_default +
              '"></label>';
            control_html +=
              '<input type="radio" name="' +
              name +
              '" id="' +
              name +
              '_hide" value="hide"' +
              (optionValue == "hide" ? " checked" : "") +
              ">";
            control_html +=
              '<label class="fa fa-eye-slash"for="' +
              name +
              '_hide"  title="' +
              alpha_layout_vars.text_hide +
              '"></label>';
            control_html +=
              '<input type="radio" name="' +
              name +
              '" id="' +
              name +
              '_my" value="my"' +
              (optionValue && optionValue != "hide" ? " checked" : "") +
              ">";
            control_html +=
              '<label class="far fa-hdd" for="' +
              name +
              '_my" title="' +
              alpha_layout_vars.text_my_templates +
              '"></label>';
            control_html += "</div>";
            // control_html += '<div class="alpha-radio-button-extend">';
            // control_html += '<a href="#" class="alpha-add-new-template fa fa-plus"></a>';
            // control_html += '<a href="#" class="far fa-edit"></a>';
            // control_html += '</div>';

            control_html +=
              '<select class="alpha-layout-part-select alpha-layout-part-control" id="' +
              name +
              '" name="' +
              name +
              '">';
            for (var block in blocks) {
              control_html +=
                '<option value="' +
                block +
                '"' +
                (optionValue == block ? " selected" : "") +
                ">" +
                blocks[block] +
                "</option>";
            }
            control_html += "</select>";
            if (alpha_layout_vars.template_builder[blockType]) {
              if (blockType == "sidebar") {
                control_html +=
                  '<a href="' +
                  window.location.href.substr(
                    0,
                    window.location.href.indexOf("wp-admin") + 8
                  ) +
                  '/admin.php?page=alpha-sidebar" target="_blank" class="new-block-template">' +
                  wp.i18n.__("Please create a new ", "alpha") +
                  blockType +
                  "</a>";
              } else {
                control_html +=
                  '<a href="' +
                  (window.location.href.substr(
                    0,
                    window.location.href.indexOf("wp-admin") + 8
                  ) +
                    "/edit.php?post_type=" +
                    alpha_admin_vars.theme +
                    "_template&" +
                    alpha_admin_vars.theme +
                    "_template_type=" +
                    blockType) +
                  '" target="_blank" class="new-block-template">' +
                  wp.i18n.__("Please create a new ", "alpha") +
                  (blockType == "product_layout"
                    ? wp.i18n.__("single product", "alpha")
                    : blockType == "shop_layout"
                    ? wp.i18n.__("shop layout", "alpha")
                    : blockType) +
                  "</a>";
              }
            } else {
              control_html +=
                '<a href="' +
                window.location.href.substr(
                  0,
                  window.location.href.indexOf("wp-admin") + 8
                ) +
                '/admin.php?page=alpha-setup-wizard&step=default_plugins" class="new-block-template">' +
                wp.i18n.__("Please activate ", "alpha") +
                alpha_admin_vars.theme_display_name +
                " Core</a>";
            }
            control_html += "</div>";
          } else if ("number" == control.type) {
            if (optionValue) {
              // check min, max validation.
              var value = optionValue;
              "undefined" != typeof control.min &&
                (value = Math.max(control.min, value));
              "undefined" != typeof control.max &&
                (value = Math.min(control.max, value));
              optionValue != value &&
                LayoutBuilderModel.setConditionOption(
                  currentCategory,
                  conditionNo,
                  optionName,
                  value
                );
            }

            control_html +=
              '<input type="number" class="alpha-layout-part-control" name="' +
              name +
              '" id="' +
              name +
              '"' +
              ("undefined" == typeof control.min
                ? ""
                : ' min="' + control.min + '"') +
              ("undefined" == typeof control.max
                ? ""
                : ' max="' + control.max + '"') +
              ' step="1" value="' +
              optionValue +
              '">';
          } else if ("select" == control.type) {
            control_html +=
              '<select class="alpha-layout-part-select alpha-layout-part-control" id="' +
              name +
              '" name="' +
              name +
              '">';
            for (var choice in control.options) {
              control_html +=
                '<option value="' +
                choice +
                '"' +
                (choice == optionValue ? " selected" : "") +
                ">" +
                control.options[choice] +
                "</option>";
            }
            control_html += "</select>";
          } else if ("text" == control.type) {
            control_html +=
              '<input type="text" name="' +
              name +
              '" class="alpha-layout-part-input alpha-layout-part-control" id="' +
              name +
              '" value="' +
              optionValue +
              '"></input>';
          } else if ("toggle" == control.type) {
            control_html += '<div class="alpha-radio-button-set">';
            control_html +=
              '<input type="radio" name="' +
              name +
              '" id="' +
              name +
              '_" value=""' +
              (optionValue ? "" : " checked") +
              ">";
            control_html +=
              '<label class="fa fa-redo" for="' +
              name +
              '_" title="' +
              alpha_layout_vars.text_default +
              '"></label>';
            control_html +=
              '<input type="radio" name="' +
              name +
              '" id="' +
              name +
              '_no" value="no"' +
              (optionValue == "no" ? " checked" : "") +
              ">";
            control_html +=
              '<label class="fa fa-eye-slash" for="' + name + '_no"></label>';
            control_html +=
              '<input type="radio" name="' +
              name +
              '" id="' +
              name +
              '_yes" value="yes"' +
              (optionValue == "yes" ? " checked" : "") +
              ">";
            control_html +=
              '<label class="fa fa-check"for="' + name + '_yes"></label>';
            control_html += "</div>";
          } else if ("multicheck" == control.type) {
            control_html += "<ul>";
            if (control.options) {
              for (var choice in control.options) {
                control_html +=
                  '<li><label><input type="checkbox" name="' +
                  name +
                  '[]" value="' +
                  choice +
                  '">' +
                  control.options[choice] +
                  "</label></li>";
              }
            }
            control_html += "</ul>";
          }

          controls_html +=
            '<div class="alpha-layout-control" data-option="' +
            optionName +
            '">' +
            control_html +
            "</div>";
        }
      }

      $options.html(controls_html);

      // Show controls UI.
      $item.addClass("edit");
    },

    /**
     * Event handler to edit condition.
     * @param {Event} e
     */
    editCondition: function(e) {
      var $link = $(e.currentTarget);
      var $item;
      if ($link.is(".alpha-condition-menu > a")) {
        $item = $link.parent().data("item");
      } else {
        $item = $link.closest(".alpha-layout-item");
      }

      if ($item.hasClass("edit-condition")) {
        setTimeout(function() {
          $item.find(".alpha-scheme-list").each(function() {
            var $this = $(this);
            if ($this.data("select2")) {
              if (!$this.hasClass("ajaxselect2")) {
                $this.select2("destroy");
              }
            }
          });
        }, 300);
      } else {
        $item
          .find(".alpha-scheme-list:not(.select2-hidden-accessible)")
          .each(function() {
            var $this = $(this);
            if ($this.hasClass("ajaxselect2")) {
              var option = $this.data("load-option"),
                values = $this.data("values"),
                path =
                  alpha_layout_vars.site_url +
                  "/wp-json/ajaxselect2/v1/" +
                  option +
                  "/";

              $this.select2({
                placeholder: $this.attr("data-placeholder"),
                ajax: {
                  url: path,
                  dataType: "json",
                  data: function(params) {
                    var query = {
                      s: params.term
                    };
                    return query;
                  }
                },
                cache: true
              });

              $.ajax({
                url: path,
                dataType: "json",
                data: {
                  ids: values ? values : ""
                }
              }).then(function(ret) {
                if (ret !== null && ret.results.length > 0) {
                  jQuery.each(ret.results, function(i, v) {
                    var op = new Option(v.text, v.id, true, true);
                    $this.append(op);
                  });
                  $this.trigger({
                    type: "select2:select",
                    params: {
                      data: ret
                    }
                  });
                }
              });
            } else {
              $this.select2({
                // dropdownParent: $this.parent(),
                placeholder: $this.attr("data-placeholder")
              });
            }
          });
      }
      $item.toggleClass("edit-condition");
    },

    /**
     *
     * @param {Event} e
     */
    clickOther: function(e) {
      var $target = $(e.target);

      if (
        !$target.closest(".select2-container").length &&
        !$target.closest(".alpha-layout-item").length &&
        $target.is("body *")
      ) {
        $(".alpha-layout-item.edit").removeClass("edit");
        $(".alpha-layout-item.edit-condition").removeClass("edit-condition");
        setTimeout(function() {
          $(".alpha-layout-item .alpha-scheme-list").each(function() {
            var $this = $(this);
            if ($this.data("select2")) {
              if (!$this.hasClass("ajaxselect2")) {
                $this.select2("destroy");
              }
            }
          });
        }, 300);
      }

      this.closeContextMenu();
    },

    /**
     * Event handler to go back from editing condition.
     * @param {Event} e
     */
    goBackFromEdit: function(e) {
      var $options = $(e.currentTarget).parent(".alpha-scheme-options");
      if ($options.length) {
        $options.hide();
        setTimeout(function() {
          $options.show();
        }, 100);
      } else {
        $(e.currentTarget)
          .closest(".alpha-layout-item")
          .removeClass("edit edit-condition");
      }
    },

    /**
     * Event handler to change value for block select control.
     *
     * @since 1.0
     */
    changeBlockMode: function(e) {
      var target = e.currentTarget;
      var $target = $(target);
      var $item = $target.closest(".alpha-layout-item");
      $target
        .closest(".alpha-block-select")
        .toggleClass("inactive-my", "my" != target.value);

      target.name.startsWith("_alpha_") &&
        LayoutBuilderModel.setConditionOption(
          $item.data("category"),
          $item.data("condition-no"),
          $target.closest(".alpha-layout-control").data("option"),
          target.value
        );

      this.refreshLayoutStatus($item);
    },

    /**
     * Event handler to change layout option.
     *
     * @since 1.0
     */
    changeOptionInput: function(e) {
      var $input = $(e.currentTarget);
      var value = $input.val();
      var $block = $input.closest(".alpha-block-select");
      var name = e.currentTarget.name;

      if ($block.length && e.currentTarget.value == "my") {
        value = $block.find("select").val();
      }
      if (name.startsWith("_alpha_")) {
        LayoutBuilderModel.setConditionOption(
          $input.closest(".alpha-layout-item").data("category"),
          $input.closest(".alpha-layout-item").data("condition-no"),
          $input.closest(".alpha-layout-control").data("option"),
          value
        );

        // Custom control conditions
        if (name.indexOf("single_product_type") >= 0) {
          var $template = $input
            .closest(".alpha-layout-options")
            .find('.alpha-layout-control[data-option="single_product_block"]');
          $template.length && $template.toggle(value == "builder");
        } else if (name.indexOf("shop_layout_type") >= 0) {
          var $template = $input
            .closest(".alpha-layout-options")
            .find('.alpha-layout-control[data-option="shop_block"]');
          var $others = $input
            .closest(".alpha-layout-options")
            .find(
              '.alpha-layout-control[data-option="products_column"], .alpha-layout-control[data-option="loadmore_type"]'
            );
          $template.length && $template.toggle(value == "builder");
          $others.length && $others.toggle(value != "builder");
        }
      }

      this.refreshLayoutStatus(
        $(e.currentTarget).closest(".alpha-layout-item")
      );
    }
  };

  /**
   * Layout Builder Class
   *
   * @since 1.0
   */
  var LayoutBuilder = {
    init: function() {
      if (
        $("#alpha_layout_content").length &&
        "undefined" != typeof alpha_layout_vars
      ) {
        this.model.init();
        this.view.init();
        this.filter();
        // $( window ).on( 'scroll show_btn_header', this.showHeaderSaveBtn );

        if ( alpha_layout_vars.default_cat ) {
          $('.alpha-layout-builder-categories [data-category="' + alpha_layout_vars.default_cat + '"]').trigger('click');
        }
      }
    },
    /**
     * Filter Layout Panels
     *
     * @since 1.0
     */
    filter: function() {
      var params = window.location.search.substring(1).split("&"),
        requests = {},
        $panel;

      params.forEach(function(item) {
        let tempArr = item.split("=");
        requests[tempArr[0]] = tempArr[1];
      });

      if (!requests["category"]) {
        return;
      }
      // Show only filter panel.
      $(".alpha-layout-item")
        .css("top", "-9999px")
        .each(function() {
          var $this = $(this);
          if (
            requests["category"] == $this.data("category") &&
            requests["index"] == $this.data("condition-no")
          ) {
            $this.css({ left: "0", top: "0" });
            $panel = $this;
          }
        });

      // Trigger Click Event
      $panel.find('[data-part="' + requests["slug"] + '"]').trigger("click");
    },
    showHeaderSaveBtn: function() {
      var $adminHeader = $(".alpha-admin-header");
      var $saveBtn = $(
        "#alpha_layout_builder .alpha-layouts-save.require-save"
      );

      if (
        $saveBtn.length &&
        $(window).scrollTop() + $adminHeader.outerHeight() >=
          $saveBtn.offset().top + $saveBtn.outerHeight()
      ) {
        $adminHeader.addClass("show-btn");
      } else {
        $adminHeader.removeClass("show-btn");
      }
    },
    view: LayoutBuilderView,
    model: LayoutBuilderModel
  };

  /**
   * Setup Layout Builder
   */
  themeAdmin.LayoutBuilder = LayoutBuilder;
  $(document).ready(function() {
    LayoutBuilder.init();

    // Add class for layout builder wrap in elementor preview
    if (location.href.indexOf("noheader") != -1) {
      $(document.body)
        .addClass("alpha-admin-page")
        .parent()
        .addClass("alpha-studio-popup");
    }
  });
})(jQuery);
