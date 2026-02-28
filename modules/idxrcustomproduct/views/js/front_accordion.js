"use strict";
var CustomizationModuleStarted = false;
const CustomizationModule = (() => {

    const invirement = 'production';
    // const invirement = 'development';

    var svg, shapeGroup;
    var cube = {
        on: false,
        scoleColor: '#000000',
        faces: 5,
        socleHeight: 0,
        vitrineWidth: 0,
        vitrineHeight: 0,
        vitrineDepth: 0,
        socleThikness: 10,
        base: true,
    };
    var shapeSettings = {
        type: 1,
        x: 0,
        y: 0,
        start: 1,
    };
    var holesSettings = {
        type: 0,
        width: 0,
        height: 0,
    };
    var cutSettings = {
        type: 0,
        width: 0,
        height: 0,
    };
    var preDecoupSetting = {
        width: 0,
        height: 0,
    }

    const textOffset = 20;
    const extraSpace = 40;
    const offset = 15;

    var scaleFactor = 1;
    var width_cm = 0;
    var height_cm = 0;
    var depth_cm = 0;
    var perimeter = 1; 
    var perimeter2 = 0;

    const setElementsToDraw = [{
            id: 'text_3',
            default: 200
        },
        {
            id: 'text_4',
            default: 100
        },
        {
            id: 'text_109',
            default: 0
        },
        {
            id: 'text_110',
            default: 0
        },
        {
            id: 'text_111',
            default: 0
        },
        {
            id: 'text_112',
            default: 0
        },
        {
            id: 'text_9',
            default: 200
        },
        {
            id: 'text_10',
            default: 150
        },
        {
            id: 'text_11',
            default: 200
        },
        {
            id: 'text_13',
            default: 5
        },
        {
            id: 'text_57',
            default: 100
        },
        {
            id: 'text_58',
            default: 40
        },
        {
            id: 'text_53',
            default: 100
        },
        {
            id: 'text_54',
            default: 100
        },
        {
            id: 'text_55',
            default: 40
        },
        {
            id: 'text_56',
            default: 40
        },
        {
            id: 'text_83',
            default: 200
        },
        {
            id: 'text_82',
            default: 200
        }
    ];
    const shapesToDraw = [{
            id: 1,
            fields: ['text_3', 'text_4', 'text_109', 'text_110', 'text_111', 'text_112']
        },
        {
            id: 2,
            fields: ['text_9']
        },
        {
            id: 3,
            fields: ['text_9']
        },
        {
            id: 4,
            fields: ['text_3', 'text_4']
        },
        {
            id: 5,
            fields: ['text_3', 'text_4', 'text_10']
        },
        {
            id: 6,
            fields: ['text_3', 'text_4', 'text_10']
        },
        {
            id: 7,
            fields: ['text_3', 'text_10']
        },
        {
            id: 8,
            fields: ['text_10', 'text_11']
        },
        {
            id: 9,
            fields: ['text_10', 'text_11']
        },
        {
            id: 10,
            fields: ['text_10', 'text_11']
        },
        {
            id: 11,
            fields: ['text_9']
        },
        {
            id: 12,
            fields: ['text_53', 'text_54', 'text_55', 'text_56']
        },
        {
            id: 13,
            fields: ['text_13', 'text_57', 'text_58']
        },
        {
            id: 14,
            fields: ['text_6', 'text_81', 'text_82', 'text_83']
        },
        {
            id: 15,
            fields: ['text_71', 'text_70']
        },
    ];
    const inputsToDraw = [
        'text_3', 'text_4', 'text_109', 'text_110', 'text_111', 'text_112', 'text_9', 'text_10',
        'text_11', 'text_53', 'text_54', 'text_55', 'text_56',
        'text_13', 'text_57', 'text_58', 'text_6', 'text_81', 'text_82', 'text_83',
        'text_71', 'text_70'
    ];

    const shapesToHole = [{
            id: 1,
            fields: ['text_18', 'text_21', 'text_22', 'text_23']
        },
        {
            id: 2,
            fields: ['text_18', 'text_25', 'text_26', 'text_27', 'text_28']
        },
        {
            id: 3,
            fields: ['text_18', 'text_21', 'text_22', 'text_23']
        },
    ];

    const inputsToHole = ['text_18', 'text_25', 'text_26', 'text_27', 'text_28', 'text_21', 'text_22', 'text_23']

    const shapesToCut = [{ // rect 
            id: 1,
            fields: ['text_38', 'text_39', 'text_40', 'text_41', 'text_42', 'text_113', 'text_114', 'text_115', 'text_116']
        },
        { // circle
            id: 2,
            fields: ['text_40', 'text_41', 'text_45']
        },
        { // demi-circle
            id: 3,
            fields: ['text_40', 'text_41', 'text_45', 'text_42']
        },
        { // ellips
            id: 4,
            fields: ['text_38', 'text_39', 'text_40', 'text_41', 'text_42']
        },
        { // trip right
            id: 5,
            fields: ['text_38', 'text_39', 'text_40', 'text_41', 'text_42', 'text_63']
        },
        { // trip strict
            id: 6,
            fields: ['text_38', 'text_39', 'text_40', 'text_41', 'text_42', 'text_63']
        },
        { // door
            id: 7,
            fields: ['text_38', 'text_39', 'text_40', 'text_41', 'text_42']
        },
        { // rect 1
            id: 8,
            fields: ['text_38', 'text_39', 'text_40', 'text_41', 'text_42']
        },
        { // rect 2
            id: 9,
            fields: ['text_38', 'text_39', 'text_40', 'text_41', 'text_42']
        },
        { // rect 3
            id: 10,
            fields: ['text_38', 'text_39', 'text_40', 'text_41', 'text_42']
        },
        { // hexagone
            id: 11,
            fields: ['text_40', 'text_41', 'text_45', 'text_42']
        },
        { // arrow
            id: 12,
            fields: ['text_40', 'text_41', 'text_42', 'text_66', 'text_67', 'text_68', 'text_69']
        },
        { // star
            id: 13,
            fields: ['text_40', 'text_41', 'text_42', 'text_64', 'text_65']
        },
    ];

    const inputsToCut = [
        'text_38', 'text_39', 'text_40', 'text_41', 'text_42',
        'text_113', 'text_114', 'text_115', 'text_116', 'text_45',
        'text_63', 'text_64', 'text_65', 'text_66', 'text_67', 
        'text_68', 'text_69', 'text_64', 'text_65'
    ];

    function init() {
        // if (!CustomizationModuleStarted) {
        //     UImanipulation();
        //     CustomizationModuleStarted = true;
        // }
        UImanipulation();
        addEnentsListeners();
        setDefaultDraw();
        setDefaultHoles();
        setDefaultCut();
        // drawShape();
    }

    function initCube() {
        cube.on = true;
        // if (!CustomizationModuleStarted) {
        //     UImanipulation();
        //     CustomizationModuleStarted = true;
        // }
        UImanipulation();
        svg = Snap("#actualSvg");
        drawCube(true);
        addCubeEnentsListeners();

        setTimeout(function() {
            $('#js_icp_next_opt_76').click();
            $('#js_icp_next_opt_77').click();
            $('#js_icp_next_opt_78').click();
            $('#js_icp_next_opt_87').click();
            // show2DView();
        }, 3000);
        // show2DView();
    }

    function reStart(num = 0) {
        if (num === 1) {
            cutSettings.type = 0;
            holesSettings.type = 0;
            unsetElement(17);
            unsetElement(29);
            setDefaultHoles();
            setDefaultCut();
        } else if (num === 2) { 
            setDefaultHoles();
            setDefaultCut();
        }

        drawShape();
        showImages();
        calculateWeight();
        if (typeof updateTotale !== 'undefined') {
            updateTotale();
        }
        if (shapeSettings.type === 14) {
            $('#js_icp_next_opt_6').click();
            $('#js_icp_next_opt_81').click();
            $('#js_icp_next_opt_82').click();
            $('#js_icp_next_opt_83').click();
        }
    }

    function setDemensions(w, h, d = 0, i_shape = '--') {
        holesSettings.width = w;
        holesSettings.height = h;
        cutSettings.width = w;
        cutSettings.height = h;
        if (!cube.on) {
            height_cm = Math.max(w, h);
            width_cm = Math.min(w, h);
        } else {
            
            cube.vitrineWidth = w / 1000;
            cube.vitrineHeight = h / 1000;
            cube.vitrineDepth = d / 1000;

            const newH = h + cube.socleHeight;
            height_cm = Math.max(w, newH, d);
            depth_cm = Math.min(w, newH, d);
            width_cm = w + newH + d - height_cm - depth_cm;
        }

        $('#product-title-unique-12345').text(i_shape);
        if (cube.on) {
            $('#product-size-unique-12345').text(`${width_cm.toFixed(2)} x ${height_cm.toFixed(2)} x ${depth_cm.toFixed(2)} mm`);
        } else {
            $('#product-size-unique-12345').text(`${width_cm.toFixed(2)} x ${height_cm.toFixed(2)} mm`);
        }
    }

    function getInputValue(id, def = 0) {
        const value = $(`#${id}`).val();
        const parsedValue = parseFloat(value);
        if (['text_25', 'text_26', 'text_40', 'text_41'].includes(id)) {
            return isNaN(parsedValue) ? def : parsedValue;
        }

        return isNaN(parsedValue) ? def : Math.abs(parsedValue);
    }

    function setInputValue(id, value) {
        const parsedValue = value !== null ? value : '';
        $(`#${id}`).val(parsedValue);

    }

    function unsetElement(id) {
        let shortText = idxr_tr_not_selected;
        $(`#resume_price_block_${id}_1 .option_title`).html(shortText);
        $(`#js_opt_${id}_value`).html('false');
        $(`#js_opt_extra_${id}_value`).html('false');
        $(`#js_opt_${id}_value_wqty`).html('false');
        $(`#step_title_${id} .check`).removeClass('check');
    }

    function generaleListners() {


        $('.accordion_text').on('input', function () {
            let inputField = $(this);
            let value = inputField.val().trim();
          
            // Normalize decimal separator
            value = value.replace(',', '.');
          
            // Prevent more than one decimal point
            const parts = value.split('.');
            if (parts.length > 2) {
              value = parts[0] + '.' + parts.slice(1).join('');
              inputField.val(value);
            }
          
            // Check for more than 1 digit after decimal
            const decimalPart = parts[1];
            if (decimalPart && decimalPart.length > 1) {
              displayError(inputField, idxr_tr_one_decimal);
              return;
            } else {
              removeInputError(inputField);
            }
          
            // Proceed if input is valid
            if (checkInputRange(this.id)) {
              inputField.closest('.step_content').addClass("finished");
              const id_option = inputField.attr('id').replace('text_', '');
              $('#js_icp_next_opt_' + id_option).click();
            }
          });
          
    //     $('.accordion_text').on('input', function() {
    //       let inputField = $(this);
    //       let value = inputField.val();
      
    //       // Fix: Prevent more than one decimal point
    //       const parts = value.split('.');
          
    //       if (parts.length > 2) {
    //           value = parts[0] + '.' + parts.slice(1).join('');
    //           inputField.val(value);
    //       }
      
    //       if (checkInputRange(this.id)) {
    //           inputField.closest('.step_content').addClass("finished");
    //           var id_option = inputField.attr('id').replace('text_', '');
    //           $('#js_icp_next_opt_' + id_option).click();
    //       }
    //   });
      
        $('.accordion_text').on('change', function() {
            const inputId = $(this).attr('id');
            const limits = inputLimits[inputId];
            
            if (limits) {
                let value = parseFloat($(this).val());
                
                // Check if value is within range; if not, set to minimum
                if (isNaN(value) || value < limits.min) {
                    $(this).val(limits.min);
                } else if (value > limits.max) {
                    $(this).val(limits.max);
                }
            }
        });            

        function moveAlert() {
            var alertDiv = $('#submit_idxrcustomproduct_alert');
            var targetDiv = $('.braig_addtocart_section');

            if (alertDiv.length && targetDiv.length) {
                alertDiv.prependTo(targetDiv);
            }
        }

        function controllers() {

            $('.zoom-in').click(function() {
                var svg = $('#actualSvg');
                var currentScale = svg.data('scale') || 1;
                svg.data('scale', currentScale + 0.1);
                svg.css('transform', `scale(${currentScale + 0.1})`);
            });

            $('.zoom-out').click(function() {
                var svg = $('#actualSvg');
                var currentScale = svg.data('scale') || 1;
                if (currentScale > 0.1) svg.data('scale', currentScale - 0.1);
                svg.css('transform', `scale(${currentScale - 0.1})`);
            });

            $('.rotateright').click(function() {
                var svg = $('#actualSvg');
                var currentRotate = svg.data('rotate') || 0;
                svg.data('rotate', currentRotate + 90);
                svg.css('transform', `rotate(${currentRotate + 90}deg)`);
            });
            $('.rotateleft').click(function() {
                var svg = $('#actualSvg');
                var currentRotate = svg.data('rotate') || 0;
                svg.data('rotate', currentRotate - 90);
                svg.css('transform', `rotate(${currentRotate - 90}deg)`);
            });

        }

        function appendHtmlBasedOnScreenWidth() {

            // Define the HTML content with two empty rows for product-short-desc and wk-sample-block
            var htmlContent = `
            <div class="container mt-2" id="containerOfBioDetails">
                <div class="row" id="wk-sample-block-row"></div>
                <div class="row" id="product-short-desc-row"></div>
            </div>
            `;
            
            if ($(window).width() >= 992) {
                $('.col-lg-6.col-image').append(htmlContent);
            } else {
                $('#product-container-bottom').prepend(htmlContent);
            }
          
              var observer = new MutationObserver(function() {
                  // Check if .wk-sample-block is present
                  if ($('.wk-sample-block').length) {
                      $('.wk-sample-block').each(function() {
                          $('#wk-sample-block-row').append($(this));
                      });
                  }
          
                  // Extract product ID from body class
                  var productId = $('body').attr('class').match(/product-id-(\d+)/);
                  if (productId) {
                      productId = productId[1];  // Get the numeric product ID from the match
          
                      // Check if the corresponding product description exists
                      var productDesc = $('#product-description-short-' + productId);
                      if (productDesc.length) {
                          // Append the product description to the appropriate row
                          $('#product-short-desc-row').append(productDesc);
                          $('.card-header .fa-info-circle').hide();
                      }
                  }
                  observer.disconnect();
              });
          
              observer.observe(document.body, {
                  childList: true,
                  subtree: true
              });
        }
        

        // addToCart();
        controllers();
        // updateExistingQuantity();
        moveAlert();
        // appendHtmlBasedOnScreenWidth();

        const observer = new MutationObserver((mutations, observer) => {
            const owlStage = document.querySelector('.owl-stage-outer .owl-stage');
            if (owlStage) {
                addEventListenersToThumbnails();
            }
            observer.disconnect();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        })

        $(window).resize(function() {
            // $('#containerOfBioDetails').remove();
            // appendHtmlBasedOnScreenWidth();
            if (typeof(idxcp_console_state) != 'undefined') {
                idxcp_console_state = 1;
            }
        });

    }

    function openCubeFields(start=false) {
        const stepIds = ['76', '77', '78', '87'];
        // const stepIds = ['76', '77', '78', '84', '85', '86', '87', '90', '91', '92', '93', '107'];

        stepIds.forEach(function(id) {

            $('#step_title_' + id).addClass('in');

            $('#component_step_' + id + ' a').each(function() {
                $(this).removeAttr('href');
                $(this).removeAttr('data-toggle');
            });
        });
        if(start){
            const stepIds = ['92', '107', '122'];
            stepIds.forEach(function(id) {
                $('#step_title_' + id).addClass('in');
            });
            
        }
    }

    function addCubeEnentsListeners() {

        function optionsEvents() {
            
            $('#card_85_0').on('click', function(){
                $('#cube_modele_de_socle').val('false');
                cube.socleThikness = 10/1000;
                $('#text_87').val(50);
                $('#text_87').prop('disabled', false);
            });
            $('#card_85_1').on('click', function(){
                $('#cube_modele_de_socle').val('true');
                $('#text_87').val(15).trigger('input');
                $('#text_87').prop('disabled', true);
            });
            $('#card_86_0').on('click', function(){
                $('#cube_materiaux_price').val('131.23');
            });
            $('#card_86_1').on('click', function(){
                $('#cube_materiaux_price').val('129.23');
            });
            $('#card_86_2').on('click', function(){
                $('#cube_materiaux_price').val('116.60');
            });
            $('#card_84_0').on('click', function() {
                $('#component_step_78').css('width', '50%');
                cube.base = true;
                var tmpHeight = $('#text_78').val() * 1.5;
                $('#text_87').val(tmpHeight).trigger('input');
                cube.socleHeight = tmpHeight;
                unsetElement(85);
                unsetElement(86);
                $('#parametres_de_socle').show();
                drawCube();
            });
            $('#card_84_1').on('click', function() {
                $('#component_step_78').css('width', '100%');
                $('#text_87').val(0);
                $('#parametres_de_socle').hide();
                $('#cube_modele_de_socle').val('false');
                $('#cube_materiaux_price').val('0');
                cube.base = false;
                cube.socleHeight = 0;
                drawCube();
            });

            $('#card_86_0').on('click', function() {
                cube.scoleColor = '#000000';
                drawCube();
            });
            $('#card_86_1').on('click', function() {
                cube.scoleColor = '#ffffff';
                drawCube();
            });
            $('#card_86_2').on('click', function() {
                cube.scoleColor = 'lightblue';
                drawCube();
            });

            $('#card_107_1').on('click', function() {
                cube.faces = 5;
                drawCube();
            });
            $('#card_122_1').on('click', function() {
                cube.faces = 5;
                drawCube();
            });
            $('#card_122_0').on('click', function() {
                cube.faces = 4;
                drawCube();
            });
            $('#card_107_0').on('click', function() {
                cube.faces = 4;
                drawCube();
            });
            $('#card_93_0').on('click', function() {
                cube.faces = 4;
                drawCube();
            });
            $('#card_93_1').on('click', function() {
                cube.faces = 5;
                drawCube();
            });
            $('#card_93_2').on('click', function() {
                cube.faces = 6;
                drawCube();
            });

        }

        generaleListners();
        optionsEvents();

        var number = 200;

        const elements = ['#text_76', '#text_77', '#text_78'];
        elements.forEach(selector => {
            $(selector).val(number).trigger('input').closest('.step_content').addClass('finished');
        });
        $('#text_87').val(number * 1.5).trigger('input').closest('.step_content').addClass('finished');
        
        // cube cards
        const clickMappings = {
            '#card_93_0, #card_93_1, #card_93_2': '#step_content_84 .card-header a',
            '#card_84_0, #card_84_1': '#step_content_85 .card-header a',
            '#card_85_0, #card_85_1': '#step_content_86 .card-header a',
            '#card_107_0, #card_107_1, #card_107_2': '#step_content_84 .card-header a',
            '#card_122_0, #card_122_1, #card_122_2': '#step_content_84 .card-header a',
            '#card_86_0, #card_86_1, #card_86_2': '#step_content_86 .card-header a'
        };
        
        // Iterate over the mappings and assign event handlers
        $.each(clickMappings, (cards, stepContent) => {
            $(cards).on('click', function() {
                setTimeout(function() {
                    $(stepContent).click();
                    setTimeout(openCubeFields, 200);
                }, 300);
            });
        });
        $('#step_title_78, #step_title_77, #step_title_76, #step_title_87').on('show.bs.collapse hide.bs.collapse', function(event) {
            event.stopPropagation(); // Prevent the event from bubbling up and affecting #step_title_78
            event.preventDefault(); // Prevent the collapse from toggling
        });
        
    }

    function addEnentsListeners() {
        generaleListners();
        initializeSvgDrag();

        function initializeSvgDrag() {
            const $dragContainer = $('#svgContainer');
            const $draggableSvg = $('#actualSvg');
            $dragContainer.css('cursor', 'grab');
        
            let isDragging = false;
            let startMouseX, startMouseY;
            let startTranslateX = 0;
            let startTranslateY = 0;
            let currentScale = 1; // Default scale
        
            // Function to get the current scale of the SVG
            function getCurrentScale() {
                const transform = $draggableSvg.css('transform');
                if (transform !== 'none') {
                    const matrix = transform.replace(/[^0-9\-.,]/g, '').split(',');
                    if (matrix.length >= 6) {
                        // Extract the scale from the transform matrix (assuming uniform scaling)
                        return parseFloat(matrix[0]);
                    }
                }
                return 1; // Default scale if no transform is set
            }
        
            // Function to handle the start of dragging
            function startDrag(event) {
                isDragging = true;
                startMouseX = event.pageX;
                startMouseY = event.pageY;
        
                // Get the current transform values
                const transform = $draggableSvg.css('transform');
                const matrix = transform.replace(/[^0-9\-.,]/g, '').split(',');
        
                if (matrix.length >= 6) {
                    startTranslateX = parseFloat(matrix[4]);
                    startTranslateY = parseFloat(matrix[5]);
                } else {
                    startTranslateX = 0;
                    startTranslateY = 0;
                }
        
                // Get the current scale dynamically
                currentScale = getCurrentScale();
        
                // Change cursor to grabbing
                $dragContainer.css('cursor', 'grabbing');
        
                event.preventDefault(); // Prevent text selection
            }
        
            // Function to handle dragging movement
            function dragMove(event) {
                if (isDragging) {
                    const deltaX = event.pageX - startMouseX;
                    const deltaY = event.pageY - startMouseY;
        
                    // Update the position of the SVG with dynamic scale
                    $draggableSvg.css('transform', `translate(${startTranslateX + deltaX}px, ${startTranslateY + deltaY}px) scale(${currentScale})`);
                }
            }
        
            // Function to stop dragging
            function stopDrag() {
                if (isDragging) {
                    isDragging = false;
                    // Revert cursor to grab
                    $dragContainer.css('cursor', 'grab');
                }
            }
        
            // Event listeners
            $dragContainer.on('mousedown', startDrag);
            $(document).on('mousemove', dragMove);
            $(document).on('mouseup', stopDrag);
        
            // Set initial cursor to grab
            $dragContainer.css('cursor', 'grab');
        }
        
        function removeClassess() {
            $('#step_title_2 .collapse').removeClass('collapse');
            $('#step_title_2 .collapse.in').removeClass('collapse in');
        }
        $('#card_2_3').on('click', function() {
            window.open('/contact-plexi-cindar', '_blank');
        });
        $('#card_52_0').on('click', function() {
            $('#idxr_is_rectangle_polissage').val('false').change();
        });
        $('#card_52_1').on('click', function() {
            $('#idxr_is_rectangle_polissage').val('true').change();
        });
        $(' #card_2_1, #card_2_2, #card_2_3').on('click', function() {
            $('#idxr_is_rectangle').val('false').change();
        });
        $('#card_2_0').on('click', function() {
            $('#idxr_is_rectangle').val('true').change();
        });
        $('#card_17_0').on('click', function() {
            inputsToHole.forEach((input)=>{
                const id = input.split('_')[1];
                if(id) unsetElement(id);
            });
        });
        $('#card_29_0').on('click', function() {
            inputsToCut.forEach((input)=>{
                const id = input.split('_')[1];
                if(id) unsetElement(id);
            });
        });
        $('#card_61_1').on('click', function() {
            $('#idxr_is_predecoupe').val('true');
            setTimeout(function() {
                $('#step_content_62 .card-header a').click();
                $('#step_content_94 .card-header a').click();
                $('#step_content_96 .card-header a').click();
                $('#step_content_97 .card-header a').click();
                $('#step_content_117 .card-header a').click();
                $('#step_content_98 .card-header a').click();
                $('#step_content_99 .card-header a').click();
                $('#step_content_100 .card-header a').click();
                $('#step_content_101 .card-header a').click();
                $('#step_content_102 .card-header a').click();
                $('#step_content_103 .card-header a').click();
                $('#step_content_104 .card-header a').click();
            }, 500);
        });
        function resetFields() {
            $('#product_surface').val(0);
            $('#resume_prix_de_decoupe_price').val('0');
            $('#diameter_de_decoupe_price').val('0');
            $('#diameter_de_decoupe_price2').val('0');
            $('#resume_price_from_cube').val('0');
            $('#idxr_is_rectangle').val('false');
            $('#idxr_is_rectangle_polissage').val('false');
            $('#idxr_is_predecoupe').val('false');
            $('#idxr_prix_de_predecoupe').val('0');
            unsetElement(2);
            setDefaultDraw();
        }
        
        $('#card_61_0').on('click', function() {
            resetFields();
            setTimeout(function() {
                $('#step_content_2 .card-header a').click();
            }, 500);
        });
        $('#card_29_1').click(function() {
            unsetElement(31);
            setDefaultCut();
        });
        $('#card_2_1').click(function() {
            unsetElement(5);
        });
        $('.card-header a').on('click', function() {
            removeClassess();
        });
        $('.qwerty-switch-wrapper').on('click', function() {
            $('.activeDemensions').toggle();
            $(this).toggleClass('active');
        });

        // function shapeTypeChange(val) {
        //     const inputGroupDiv = $('#fieldsHolderStepOne');
        //     if(inputGroupDiv && inputGroupDiv.length > 0) inputGroupDiv.find('.measurements-selector__error').remove();

        //     if (document.getElementById('TwoDImageThumb')) document.getElementById('TwoDImageThumb').click();
        //     return function() {
        //         shapeSettings.type = val;
        //         removeClassess();
        //         reverseSelect(shapeSettings.type, 1);
        //         reStart(1);
        //     };
        // }
        function shapeTypeChange(val) {
            const inputGroupDiv = $('#fieldsHolderStepOne');
            if (inputGroupDiv && inputGroupDiv.length > 0) {
                inputGroupDiv.find('.measurements-selector__error').remove();
            }
        
            if (document.getElementById('TwoDImageThumb')) {
                document.getElementById('TwoDImageThumb').click();
            }
        
            shapeSettings.type = val;
            removeClassess();
            reverseSelect(shapeSettings.type, 1);
            reStart(1);
        }
        

        function holeTypeChange(val) {
            return function() {
                holesSettings.type = val;
                reverseSelect(holesSettings.type, 2);
                reStart(2);
            };
        }

        function cutTypeChange(val) {
            return function() {
                cutSettings.type = val;
                removeClassess();
                reverseSelect(cutSettings.type, 3);
                reStart(3);
            };
        }

        const elementsToDraw = [{
                selector: '#card_2_0',
                value: 1
            },
            {
                selector: '#card_5_1',
                value: 2
            },
            {
                selector: '#card_5_12',
                value: 3
            },
            {
                selector: '#card_5_2',
                value: 4
            },
            {
                selector: '#card_5_3',
                value: 5
            },
            {
                selector: '#card_5_14',
                value: 6
            },
            {
                selector: '#card_5_4',
                value: 7
            },
            {
                selector: '#card_5_5',
                value: 8
            },
            {
                selector: '#card_5_6',
                value: 9
            },
            {
                selector: '#card_5_15',
                value: 10
            },
            {
                selector: '#card_5_8',
                value: 11
            },
            {
                selector: '#card_5_11',
                value: 13
            },
            {
                selector: '#card_5_13',
                value: 12
            },
            {
                selector: '#card_2_2',
                value: 14
            },
            {
                selector: '#card_5_16',
                value: 16
            }
        ];
        
        elementsToDraw.forEach(element => {
            $(element.selector).on('click', function () {
                shapeTypeChange(element.value);
                
                // Change text only when #card_5_8 is clicked
                if (element.selector === '#card_5_8') {
                    $("#step_content_9 .card-header-h5 a").contents().filter(function () {
                        return this.nodeType === 3; // Select text nodes only
                    }).first().replaceWith("Longueur du côté");
                } else {
                    $("#step_content_9 .card-header-h5 a").contents().filter(function () {
                        return this.nodeType === 3;
                    }).first().replaceWith(idxr_tr_diameter);
                }
            });
        });

        const elementsToHole = [{
                selector: '#card_17_0',
                value: 0
            },
            {
                selector: '#card_17_1',
                value: 1
            },
            {
                selector: '#card_17_2',
                value: 2
            },
            {
                selector: '#card_17_3',
                value: 3
            }
        ];
        const elementsToCut = [{
                selector: '#card_29_0',
                value: 0
            },
            {
                selector: '#card_31_0',
                value: 1
            },
            {
                selector: '#card_31_1',
                value: 2
            },
            {
                selector: '#card_31_12',
                value: 3
            },
            {
                selector: '#card_31_2',
                value: 4
            },
            {
                selector: '#card_31_3',
                value: 5
            },
            {
                selector: '#card_31_13',
                value: 6
            },
            {
                selector: '#card_31_4',
                value: 7
            },
            {
                selector: '#card_31_5',
                value: 8
            },
            {
                selector: '#card_31_6',
                value: 9
            },
            {
                selector: '#card_31_14',
                value: 10
            },
            {
                selector: '#card_31_8',
                value: 11
            },
            {
                selector: '#card_31_9',
                value: 12
            },
            {
                selector: '#card_31_11',
                value: 13
            },
        ];

        
        elementsToHole.forEach(element => {
            $(element.selector).on('click', holeTypeChange(element.value));
        });
        elementsToCut.forEach(element => {
            $(element.selector).on('click', cutTypeChange(element.value));
        });

        function predecopDivs() {
            // Define components with their dimensions in a simplified format
            const components = [
                { id_component: 62, dimensions: [[2050, 1550, 86.20], [1550, 1025, 42.83]] },
                { id_component: 94, dimensions: [[3050, 2030, 260.02], [2030, 2030, 173.08], [2030, 1525, 130.03], [2030, 1015, 86.52], [1015, 1015, 43.26]] },
                { id_component: 96, dimensions: [[3050, 2050, 220.95], [2050, 1525, 110.65]] },
                { id_component: 97, dimensions: [[2050, 1550, 86.20], [1550, 1025, 42.83]] },
                { id_component: 98, dimensions: [[3050, 2050, 184.58], [2050, 1525, 92.32], [2050, 1015, 61.43], [1020, 1015, 30.57]] },
                { id_component: 99, dimensions: [[2050, 1250, 42.92], [1250, 1025, 21.47]] },
                { id_component: 100, dimensions: [[3050, 2050, 167.25], [2050, 1525, 83.65]] },
                { id_component: 117, dimensions: [[3050, 2030, 167.25], [2030, 1525, 83.65]] },
                { id_component: 101, dimensions: [[3050, 2030, 68.93], [2030, 1525, 34.41], [2440, 1220, 33.07], [3050, 1560, 52.90], [3050, 1220, 41.43]] },
                { id_component: 102, dimensions: [[3050, 2030, 359.37], [3050, 1530, 270.54], [2030, 1525, 179.39]] },
                { id_component: 103, dimensions: [[3000, 2000, 1347.48], [2000, 1500, 673.74], [2000, 1000, 449.16]] },
                { id_component: 104, dimensions: [[1200, 2000, 831.54]] }
            ];
            // Iterate through each component and its dimensions
            components.forEach(component => {
        
                const { id_component, dimensions } = component;
        
                dimensions.forEach((dimension, index) => {
                    const cardId = `#card_${id_component}_${index}`;
                    const cardElement = $(cardId);
        
                    if (cardElement.length > 0) {
                        // Add the event listener to the card
                        cardElement.on('click', function () {
                            const [dem1, dem2, price] = dimension; // Directly extract dimensions
                            // console.log(`Card clicked: ID=${id_component}, Dimension=${dem1} x ${dem2}`);
                            
                            // Call the drawRect function with separated dimensions
                            preDecoupSetting.width = Math.min(dem1, dem2);
                            preDecoupSetting.height = Math.max(dem1, dem2);
                            $('#idxr_prix_de_predecoupe').val(price);
                            shapeTypeChange(15);
                            
                            $('#TwoDImageThumb').click();
                            shapeSettings.type = 15;
                            removeClassess();
                            reverseSelect(shapeSettings.type, 1);
                            reStart(1);
                        });
                    } 
                    // else {
                    //     console.warn(`Card not found: ${cardId}`);
                    // }
                });
            });
        }
        predecopDivs();
    }
    
    function addEventListenersToThumbnails() {
        addThumbnails('/modules/idxrcustomproduct/img/icon/2d.png', 'TwoDImageThumb');

        const thumbnails = document.querySelectorAll('.owl-stage .thumb.js-thumb');

        thumbnails.forEach((thumb, index) => {
            const parentOwlItem = thumb.closest('.owl-item');
            if (index !== thumbnails.length - 1 || (parentOwlItem && parentOwlItem.id !== 'TwoDImageThumb')) {
                thumb.addEventListener('click', show2DView, { once: true });
            } else {
                thumb.addEventListener('click', showImages, { once: true });
            }
        });
    }

    function UImanipulation() {

        function Extrac() {
            $('.col-lg-6.col-image').addClass('custom-image');
        }

        function waitForElement(selector, callback) {
            const observer = new MutationObserver((mutations, observer) => {
                if (document.querySelector(selector)) {
                    callback();
                }
                observer.disconnect();
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }

        function titleHolderTitle() {

            let titleHolder = document.getElementById('titleHolder');
            if (!titleHolder) {
                titleHolder = document.createElement('div');
                titleHolder.id = 'titleHolder';
            }

            const parentElement = document.querySelector('.col-lg-6.col-content .col-content-inside');
            if (!parentElement) {
                console.error('Parent element not found');
                return;
            }

            waitForElement('.price-information', () => {
                if(cube.on){
                    $('.price-information').hide();
                }else{
                    const priceInfo = document.querySelector('.price-information');
                    const infoProd = document.querySelector('.info-prod');
                    const productComments = document.querySelector('.product-comments-additional-info');
                    const productTitle = document.querySelector('.h1.product-title');

                    [productTitle, productComments, infoProd, priceInfo].forEach(element => {
                        if (element) {
                            titleHolder.appendChild(element);
                        }
                    });

                    if (parentElement.firstChild) {
                        parentElement.insertBefore(titleHolder, parentElement.firstChild);
                    } else {
                        parentElement.appendChild(titleHolder);
                    }
                }
            });
        }

        function changePositions() {
            function removeCollapseClassesAndAttributes(elements) {
                elements.forEach(element => {
                    element.classList.remove('collapse');
                    element.removeAttribute('aria-expanded');
                    element.removeAttribute('style');
                });
            }

            function removeAnchorAttributes(elements) {
                elements.forEach(anchor => {
                    anchor.removeAttribute('href');
                    anchor.removeAttribute('data-toggle');
                });
            }

            function appendComponents(componentIds, parentElement) {
                componentIds.forEach(id => {
                    let componentStep = document.getElementById(id);
                    if (componentStep) {
                        parentElement.appendChild(componentStep);
                        removeAnchorAttributes(componentStep.querySelectorAll('a'));
                        removeCollapseClassesAndAttributes(componentStep.querySelectorAll('.collapse'));
                    }
                });
            }

            function createAndAppendContainer(parentElement, containerId) {
                let containerDiv = document.createElement('div');
                containerDiv.id = containerId;
                containerDiv.className = "movedDivsHolder";
                parentElement.appendChild(containerDiv);
                return containerDiv;
            }

            function handleComponentStep(stepId, componentIds, containerId, additionalComponents = []) {
                let componentStep = document.getElementById(stepId);
                if (componentStep) {
                    let targetDiv = componentStep.querySelector('.card.step_content');
                    let targetDiv2 = targetDiv?.querySelector('.card-block');
                    if (targetDiv2) {
                        let fieldsHolder = createAndAppendContainer(targetDiv2, containerId);
                        appendComponents(additionalComponents, fieldsHolder);
                        appendComponents(componentIds, fieldsHolder);
                    }
                }
            }

            document.querySelectorAll('.card.step_content .panel-collapse').forEach(targetDiv => {
                targetDiv.classList.remove('panel-collapse');
            });

            let step2ComponentIds = [
                'component_step_6', 'component_step_83', 'component_step_3', 'component_step_4',
                'component_step_109','component_step_110','component_step_111','component_step_112',
                'component_step_9', 'component_step_10', 'component_step_11',
                'component_step_12', 'component_step_13', 'component_step_53', 'component_step_54',
                'component_step_56', 'component_step_57', 'component_step_58', 'component_step_55'
            ];

            let step17ComponentIds = [
                'component_step_14', 'component_step_15', 'component_step_16', 'component_step_18',
                'component_step_19', 'component_step_20', 'component_step_21', 'component_step_22',
                'component_step_23', 'component_step_24', 'component_step_25', 'component_step_26',
                'component_step_27', 'component_step_28'
            ];

            let step29ComponentIds = [
                'component_step_32', 'component_step_33', 'component_step_34',
                'component_step_35', 'component_step_36', 'component_step_37', 'component_step_38',
                'component_step_39', 'component_step_63', 'component_step_40', 'component_step_41', 'component_step_42',
                'component_step_113', 'component_step_114', 'component_step_115', 'component_step_116', 
                'component_step_44', 'component_step_45', 'component_step_46',
                'component_step_47', 'component_step_48', 'component_step_64', 'component_step_65',
                'component_step_66', 'component_step_67', 'component_step_68', 'component_step_69'
            ];

            let step62ComponentIds = [
                'component_step_70', 'component_step_71'
            ];

            function addPerviews() {
                const stepOptionsDiv = $('<div id="step_2_options"></div>');
                const stepPreviewDiv = $('<div id="step_2_preview"></div>');
                $('#step_title_2 .card-block').prepend(stepOptionsDiv);
                $('#step_2_options').after(stepPreviewDiv);
                $('#card_2_0, #card_2_1, #card_2_2, #card_2_3').appendTo('#step_2_options');

                const stepOptionsDiv2 = $('<div id="step_17_options"></div>');
                const stepPreviewDiv2 = $('<div id="step_17_preview"></div>');
                $('#step_title_17 .card-block').prepend(stepOptionsDiv2);
                $('#step_17_options').after(stepPreviewDiv2);
                $('#card_17_0, #card_17_1, #card_17_2, #card_17_3').appendTo('#step_17_options');
                
                const stepOptionsDiv3 = $('<div id="step_29_options"></div>');
                const stepPreviewDiv3 = $('<div id="step_29_preview"></div>');
                $('#step_title_29 .card-block').prepend(stepOptionsDiv3);
                $('#step_29_options').after(stepPreviewDiv3);
                $('#card_29_0, #card_29_1').appendTo('#step_29_options');
            }

            addPerviews();
            handleComponentStep('component_step_2', step2ComponentIds, 'fieldsHolderStepOne', ['component_step_5']);
            handleComponentStep('component_step_17', step17ComponentIds, 'fieldsHolderStepHoles');
            handleComponentStep('component_step_29', step29ComponentIds, 'fieldsHolderStepCut', ['component_step_31']);
            handleComponentStep('component_step_62', step62ComponentIds, 'fieldsHolderStep2');
        }

        function insertSvgContainer() {
            // Prevent duplicate creation
            if (document.getElementById('svgContainer')) return;

            const svgContainer = document.createElement('div');
            svgContainer.className = 'svg-cover hidden';
            svgContainer.id = 'svgContainer';

            const svgElement = `
                <svg id="actualSvg" width="400" height="400" style="width: 100%; height: 100%;">
                    <g id="shapeContainer"></g>
                    <g id="holesContainer"></g>
                    <g id="couOutMain">
                        <g id="cutoutContainer"></g>
                        <g id="cutoutDems" class="activeDemensions"></g>
                    </g>
                    <g id="arrowsContainer" class="activeDemensions"></g>
                </svg>
            `;
            svgContainer.innerHTML = svgElement;

            const productCover = document.querySelector('.product-cover');

            if (productCover) {
                productCover.insertAdjacentElement('afterend', svgContainer);
            } else {
                console.error('Error: .product-cover element not found');
            }
        }


        function appendUnits() {
            const inputIds = [
                '3', '23', '4', '9', '10', '11',
                '57', '58', '53', '54', '55', '56',
                '18', '25', '26', '27',
                '38', '64', '63', '39', '65', '40', '41', '42',
                '45', '66', '67', '68', '69'
            ];
            inputIds.forEach(id => {
                const inputElement = document.getElementById(`text_${id}`);
                if (inputElement) {
                    const span = document.createElement('span');
                    span.className = 'unit';
                    if (id === '42') {
                        span.textContent = 'deg';
                    } else {
                        span.textContent = 'mm';
                    }

                    inputElement.parentNode.insertBefore(span, inputElement.nextSibling);
                } else {
                    console.error('Element not found: text_', id);
                }
            });
        }

        function topButtons() {
            var controlsHtml = `
            <div class="svg-controls">
                <button class="svg-btn zoom-in"><img src="/modules/idxrcustomproduct/img/icon/p.png" alt="rg"></button>
                <button class="svg-btn zoom-out"><img src="/modules/idxrcustomproduct/img/icon/m.png" alt="rg"></button>
                <button class="svg-btn rotateright"><img src="/modules/idxrcustomproduct/img/icon/rr.png" alt="rg"></i></button>
                <button class="svg-btn rotateleft"><img src="/modules/idxrcustomproduct/img/icon/rl.png" alt="rg"></button>

                <div class="qwerty-switch-container">
                    <p id="switchStatus">Dimensions:</p>
                    <div class="qwerty-switch-wrapper active">
                        <div class="qwerty-switch-bg">
                            <div class="qwerty-switch-circle"></div>
                        </div>
                    </div>
                </div>
            </div>`;
            $('#svgContainer').prepend(controlsHtml);

        }

        function topButtonsCube() {
            var controlsHtml = `
            <div class="svg-controls">
                <button class="svg-btn zoom-in"><img src="/modules/idxrcustomproduct/img/icon/p.png" alt="rg"></button>
                <button class="svg-btn zoom-out"><img src="/modules/idxrcustomproduct/img/icon/m.png" alt="rg"></button>
                <button class="svg-btn rotateright"><img src="/modules/idxrcustomproduct/img/icon/rr.png" alt="rg"></i></button>
                <button class="svg-btn rotateleft"><img src="/modules/idxrcustomproduct/img/icon/rl.png" alt="rg"></button>
            </div>`;
            $('#svgContainer').prepend(controlsHtml);

        }

        function openFirst() {
            if(!$('#step_title_119').length > 0){
                if (typeof loadimages !== 'undefined') loadimages($('#step_title_61'));
                $('#step_title_61').addClass('in');
            }
        }

        function openFirstCube() {
            function createSettingsDivs() {
                if ($('#parametres_de_vitrine').length === 0 && $('#component_step_92').length) {
                    const vitrineDivider = $('<div>', { class: 'divider', id: 'parametres_de_vitrine' })
                        .append($('<span>').text(idxr_tr_display_settings));
                    $('#component_step_92').before(vitrineDivider);
                }
                if ($('#parametres_de_socle').length === 0 && $('#component_step_85').length) {
                    const socleDivider = $('<div>', { class: 'divider', id: 'parametres_de_socle' })
                        .append($('<span>').text(idxr_tr_base_settings));
                    $('#component_step_85').before(socleDivider);
                }
                if ($('#parametres_de_dems').length === 0 && $('#fieldsBoxDemesions').length) {
                    const demsDivider = $('<div>', { class: 'divider', id: 'parametres_de_dems' })
                        .append($('<span>').text(idxr_tr_outer_dimensions));
                    $('#fieldsBoxDemesions').before(demsDivider);
                }
            }

            function appendInputs() {
                openCubeFields(true);

                // Only append the container if it doesn't already exist
                if ($('#fieldsBoxDemesions').length === 0 && $('#component_step_86').length) {
                    var fieldID = $('<div>', {
                        class: 'col-lg-12 component_step',
                        id: 'fieldsBoxDemesions'
                    });

                    fieldID.insertAfter('#component_step_86');

                    var fieldsHolderDiv = $('<div>', { class: 'movedDivsHolder' });
                    fieldsHolderDiv.appendTo(fieldID);

                    // Only append each component if it exists
                    const components = ['#component_step_91', '#component_step_78', '#component_step_76', '#component_step_77', '#component_step_87'];

                    components.forEach(function(selector) {
                        if ($(selector).length) {
                            $(selector).appendTo(fieldsHolderDiv);
                        }
                    });
                }
            }


            appendInputs();
            createSettingsDivs();
        }

        function appendUnitsCube() {
            const inputIds = [
                'text_76', 'text_77', 'text_78', 'text_87'
            ];
            inputIds.forEach(id => {
                const inputElement = document.getElementById(id);
                if (inputElement) {
                    const span = document.createElement('span');
                    span.className = 'unit';
                    span.textContent = 'mm';

                    inputElement.parentNode.insertBefore(span, inputElement.nextSibling);
                } else {
                    console.error('Element not found:', id);
                }
            });
        }

        function textConfig() {

            $('#text_6').attr('type', 'text');
            $('#text_6').val('Louis').trigger('input');
            $('#text_81').attr('type', 'text');
            $('#text_81').val('/modules/idxrcustomproduct/views/js/fonts/Alfa_Slab_One/AlfaSlabOne-Regular.ttf').trigger('input');

            const Fonts = `<label for="fontSelector" style="margin-right: 10px;">${idxr_tr_select_font}:</label>
            <select id="fontSelector" class="font-selector">
                <option value='' disabled selected>Select a font</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Afacad_Flux/AfacadFlux-VariableFont_slnt,wght.ttf'>Afacad_Flux - AfacadFlux-VariableFont_slnt,wght</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Alfa_Slab_One/AlfaSlabOne-Regular.ttf'>Alfa_Slab_One - AlfaSlabOne-Regular</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Anton_SC/AntonSC-Regular.ttf'>Anton_SC - AntonSC-Regular</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-Bold.ttf'>Chakra_Petch - ChakraPetch-Bold</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-BoldItalic.ttf'>Chakra_Petch - ChakraPetch-BoldItalic</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-Italic.ttf'>Chakra_Petch - ChakraPetch-Italic</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-Light.ttf'>Chakra_Petch - ChakraPetch-Light</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-LightItalic.ttf'>Chakra_Petch - ChakraPetch-LightItalic</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-Medium.ttf'>Chakra_Petch - ChakraPetch-Medium</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-MediumItalic.ttf'>Chakra_Petch - ChakraPetch-MediumItalic</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-Regular.ttf'>Chakra_Petch - ChakraPetch-Regular</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-SemiBold.ttf'>Chakra_Petch - ChakraPetch-SemiBold</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-SemiBoldItalic.ttf'>Chakra_Petch - ChakraPetch-SemiBoldItalic</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/cs-vasco-font-1730655040-0/CSVasco-Regular_demo-BF672337323e31e.otf'>cs-vasco-font-1730655040-0 - CSVasco-Regular</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Dancing_Script/DancingScript-VariableFont_wght.ttf'>Dancing_Script - DancingScript-VariableFont_wght</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Danfo/Danfo-Regular-VariableFont_ELSH.ttf'>Danfo - Danfo-Regular-VariableFont_ELSH</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-Bold.ttf'>IBM_Plex_Mono - IBMPlexMono-Bold</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-BoldItalic.ttf'>IBM_Plex_Mono - IBMPlexMono-BoldItalic</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-ExtraLight.ttf'>IBM_Plex_Mono - IBMPlexMono-ExtraLight</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-ExtraLightItalic.ttf'>IBM_Plex_Mono - IBMPlexMono-ExtraLightItalic</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-Italic.ttf'>IBM_Plex_Mono - IBMPlexMono-Italic</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-Light.ttf'>IBM_Plex_Mono - IBMPlexMono-Light</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-LightItalic.ttf'>IBM_Plex_Mono - IBMPlexMono-LightItalic</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-Medium.ttf'>IBM_Plex_Mono - IBMPlexMono-Medium</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-MediumItalic.ttf'>IBM_Plex_Mono - IBMPlexMono-MediumItalic</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-Regular.ttf'>IBM_Plex_Mono - IBMPlexMono-Regular</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-SemiBold.ttf'>IBM_Plex_Mono - IBMPlexMono-SemiBold</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-SemiBoldItalic.ttf'>IBM_Plex_Mono - IBMPlexMono-SemiBoldItalic</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-Thin.ttf'>IBM_Plex_Mono - IBMPlexMono-Thin</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-ThinItalic.ttf'>IBM_Plex_Mono - IBMPlexMono-ThinItalic</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Itim/Itim-Regular.ttf'>Itim - Itim-Regular</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Lobster/Lobster-Regular.ttf'>Lobster - Lobster-Regular</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/magion-font-1730654999-0/magiontrial-italic.otf'>magion-font-1730654999-0 - magiontrial-italic</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/magion-font-1730654999-0/magiontrial-regular.otf'>magion-font-1730654999-0 - magiontrial-regular</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Oswald/Oswald-VariableFont_wght.ttf'>Oswald - Oswald-VariableFont_wght</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Pacifico/Pacifico-Regular.ttf'>Pacifico - Pacifico-Regular</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Permanent_Marker/PermanentMarker-Regular.ttf'>Permanent_Marker - PermanentMarker-Regular</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Playfair_Display/PlayfairDisplay-Italic-VariableFont_wght.ttf'>Playfair_Display - PlayfairDisplay-Italic-VariableFont_wght</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Playfair_Display/PlayfairDisplay-VariableFont_wght.ttf'>Playfair_Display - PlayfairDisplay-VariableFont_wght</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Playwrite_GB_S/PlaywriteGBS-Italic-VariableFont_wght.ttf'>Playwrite_GB_S - PlaywriteGBS-Italic-VariableFont_wght</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Playwrite_GB_S/PlaywriteGBS-VariableFont_wght.ttf'>Playwrite_GB_S - PlaywriteGBS-VariableFont_wght</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Rubik_Wet_Paint/RubikWetPaint-Regular.ttf'>Rubik_Wet_Paint - RubikWetPaint-Regular</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/seadoh-font-1730655129-0/seadoh-demolight.otf'>seadoh-font-1730655129-0 - seadoh-demolight</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/seadoh-font-1730655129-0/seadoh-demoregular.otf'>seadoh-font-1730655129-0 - seadoh-demoregular</option>
                <option value='/modules/idxrcustomproduct/views/js/fonts/Spicy_Rice/SpicyRice-Regular.ttf'>Spicy_Rice - SpicyRice-Regular</option>
                </select>`;
            $('#component_step_6').prepend('<div class="font-selector-container">' + Fonts + '</div>');

            $('#fontSelector').on('change', function() {
                const selectedFont = $(this).val();
                $('#text_81').val(selectedFont).trigger('input');
            });

            $('#component_step_83').prepend(`
                <div class="dem-toggle-switch">
                    <span class="demension_label">${idxr_tr_desired_dimension}</span>
                    <div class="demension_switch">
                        <input type="radio" name="demension_dimension" id="demension_height" checked>
                        <label for="demension_height" class="switch-label" id="heightLabel">${idxr_tr_height}</label>
                        <input type="radio" name="demension_dimension" id="demension_width">
                        <label for="demension_width" class="switch-label" id="widthLabel">${idxr_tr_width}</label>
                        <div class="slider"></div>
                    </div>
                </div>`);
            $('input[name="demension_dimension"]').change(function() {
                if ($('#demension_width').is(':checked')) {
                    $('#text_82').attr('type', 'text').val(idxr_tr_width).trigger('input');
                    $('#step_content_83 a:first').text(idxr_tr_width);
                } else {
                    $('#text_82').attr('type', 'text').val(idxr_tr_height).trigger('input');
                    $('#step_content_83 a:first').text(idxr_tr_height);
                }
            });
        }

        function changeDivsToUrls(){
            const links = {
                "card_92_0": "/accueil/10157-vitrine-plexiglass-sur-mesure-epaisseur-4mm.html",
                "card_92_1": "/accueil/10158-vitrine-plexiglass-sur-mesure-epaisseur-5mm.html",
                "card_92_2": "/accueil/10159-vitrine-plexiglass-sur-mesure-epaisseur-6mm.html",
                "card_92_3": "/accueil/10160-vitrine-plexiglass-sur-mesure-epaisseur-8mm.html",
                "card_92_4": "/accueil/10161-vitrine-plexiglass-sur-mesure-epaisseur-10mm.html"
              };
            
              $.each(links, function(id, url) {
                const $div = $('#' + id);
                if ($div.find('.check').length === 0) {
                  $div.on('click', function() {
                    window.location.href = url;
                  });
                }
              });
        }

        function createCollapsibleTable() {
            // Main container for the fixed table
            const $fixedTable = $('<div></div>').css({
                position: 'fixed',
                bottom: '0',
                left: '0',
                backgroundColor: '#f9f9f9',
                borderTop: '1px solid #ccc',
                borderRight: '1px solid #ccc',
                boxShadow: '0 -2px 10px rgba(0, 0, 0, 0.2)',
                fontFamily: 'Arial, sans-serif',
                zIndex: '199'
            }).attr('id', 'fixedTable');
            
            // Header for the title and subtitle
            const $tableHeader = $('<div></div>').css({
                padding: '10px',
                backgroundColor: '#333',
                color: '#fff',
                cursor: 'pointer',
                textAlign: 'center'
            }).attr('id', 'tableHeader').appendTo($fixedTable);
    
            // Title
            const tableTitle = $('<p></p>').text('Schéma de tarification (réduire) ').css({
                margin: '0',
                fontSize: '18px',
                fontweight: 'bold',
                color: 'white'
            }).appendTo($tableHeader);
            
            $('<img src="https://icons.iconarchive.com/icons/paomedia/small-n-flat/256/sign-down-icon.png" width="30px" height="30px"/>').appendTo(tableTitle);

            // Subtitle
            $('<p></p>').text('Ce tableau n\'est affiché qu\'en développement, pas pour les clients.').css({
                margin: '0',
                fontSize: '14px',
                color: 'white'
            }).appendTo($tableHeader);
    
            // Table content (initially visible)
            const $tableContent = $('<div></div>').css({
                display: 'block',
                padding: '10px'
            }).attr('id', 'tableContent').appendTo($fixedTable);
    
            // Table element
            const $table = $('<table></table>').css({
                width: '100%',
                borderCollapse: 'collapse'
            }).appendTo($tableContent);
            
            const tableContent = [
                ['<b>Element</b>', '<b>Price generale HT</b>', '<b>Prix HT</b>', 'Prix TTC'],

                ['Surface de capot: <span id="s_d_capot"></span>', '<span id="price_map_1">0 €/m²</span>', '<span id="price_map_ht_1">0 €</span>', '<span id="price_map_ttc_1">0 €</span>'],
                ['Prix de découpe de capot: <span id="p_d_d_map_1"></span>', '<span id="price_map_4">0 €/m²</span>', '<span id="price_map_ht_4">0 €</span>', '<span id="price_map_ttc_4">0 €</span>'],
                ['Prix de collage de capot: <span id="p_d_c_map_1"></span>', '<span id="price_map_5">0 €/m²</span>', '<span id="price_map_ht_5">0 €</span>', '<span id="price_map_ttc_5">0 €</span>'],

                ['Surface de socle: <span id="s_d_socle"></span>', '<span id="price_map_2">0 €/m²</span>', '<span id="price_map_ht_2">0 €</span>', '<span id="price_map_ttc_2">0 €</span>'],
                ['Prix de découpe de socle: <span id="p_d_c_map_2"></span>', '<span id="price_map_6">0 €/m²</span>', '<span id="price_map_ht_6">0 €</span>', '<span id="price_map_ttc_6">0 €</span>'],
                ['Prix de collage de socle: <span id="p_d_c_map_2"></span>', '<span id="price_map_7">0 €/m²</span>', '<span id="price_map_ht_7">0 €</span>', '<span id="price_map_ttc_7">0 €</span>'],

                ['Avec épaulement', '<span id="price_map_3">0 €/m²</span>', '<span id="price_map_ht_3">0 €</span>', '<span id="price_map_ttc_3">0 €</span>'],
                ['<b>Totale</b>', '<b>--</b>', '<b><span id="price_map_totale_ht">0 €/m²</span></b>', '<b><span id="price_map_totale_ttc">0 €/m²</span></b>'],
            ];  
            // 3x6 Table body creation 
            for (let i = 0; i < 9; i++) {
                const $row = $('<tr></tr>').appendTo($table);
                for (let j = 0; j < 4; j++) {
                    $('<td></td>').html(tableContent[i][j]).css({
                        border: '1px solid #ddd',
                        padding: '8px',
                        textAlign: 'center'
                    }).appendTo($row);
                }
            }
    
            // Toggle functionality
            $tableHeader.on('click', function() {
                $tableContent.toggle();
            });
    
            // Append the table to the body
            $('body').append($fixedTable);
        }

        function createCollapsibleTable2() {
            // Main container for the fixed table
            const $fixedTable = $('<div></div>').css({
                position: 'fixed',
                bottom: '0',
                left: '0',
                backgroundColor: '#f9f9f9',
                borderTop: '1px solid #ccc',
                borderRight: '1px solid #ccc',
                boxShadow: '0 -2px 10px rgba(0, 0, 0, 0.2)',
                fontFamily: 'Arial, sans-serif',
                zIndex: '199'
            }).attr('id', 'fixedTable');
            
            // Header for the title and subtitle
            const $tableHeader = $('<div></div>').css({
                padding: '10px',
                backgroundColor: '#333',
                color: '#fff',
                cursor: 'pointer',
                textAlign: 'center'
            }).attr('id', 'tableHeader').appendTo($fixedTable);
    
            // Title
            const tableTitle = $('<p></p>').text('Schéma de tarification (réduire) ').css({
                margin: '0',
                fontSize: '18px',
                fontweight: 'bold',
                color: 'white'
            }).appendTo($tableHeader);
            
            $('<img src="https://icons.iconarchive.com/icons/paomedia/small-n-flat/256/sign-down-icon.png" width="30px" height="30px"/>').appendTo(tableTitle);

            // Subtitle
            $('<p></p>').text('Ce tableau n\'est affiché qu\'en développement, pas pour les clients.').css({
                margin: '0',
                fontSize: '14px',
                color: 'white'
            }).appendTo($tableHeader);
    
            // Table content (initially visible)
            const $tableContent = $('<div></div>').css({
                display: 'block',
                padding: '10px'
            }).attr('id', 'tableContent').appendTo($fixedTable);
    
            // Table element
            const $table = $('<table></table>').css({
                width: '100%',
                borderCollapse: 'collapse'
            }).appendTo($tableContent);
            
            const tableContent = [
                ['<b>Element</b>', '<b>Price generale HT</b>', '<b>Prix HT</b>', 'Prix TTC'],

                ['Surface: <span id="s_d_capot"></span>', '<span id="price_map_1">0 €/m²</span>', '<span id="price_map_ht_1">0 €</span>', '<span id="price_map_ttc_1">0 €</span>'],
                ['Prix des découpes: <span id="p_d_d_map_1"></span>', '<span id="price_map_4">0 €/m²</span>', '<span id="price_map_ht_4">0 €</span>', '<span id="price_map_ttc_4">0 €</span>'],
                ['Prix de polissage: <span id="p_d_c_map_1"></span>', '<span id="price_map_5">0 €/m²</span>', '<span id="price_map_ht_5">0 €</span>', '<span id="price_map_ttc_5">0 €</span>'],
                ['Plaques predecoupees:', '<span id="price_map_7">0 €/m²</span>', '<span id="price_map_ht_7">0 €</span>', '<span id="price_map_ttc_7">0 €</span>'],

                ['<b>Totale</b>', '<b>--</b>', '<b><span id="price_map_totale_ht">0 €/m²</span></b>', '<b><span id="price_map_totale_ttc">0 €/m²</span></b>'],
            ];  
            // 3x6 Table body creation 
            for (let i = 0; i < 5; i++) {
                const $row = $('<tr></tr>').appendTo($table);
                for (let j = 0; j < 4; j++) {
                    $('<td></td>').html(tableContent[i][j]).css({
                        border: '1px solid #ddd',
                        padding: '8px',
                        textAlign: 'center'
                    }).appendTo($row);
                }
            }
    
            // Toggle functionality
            $tableHeader.on('click', function() {
                $tableContent.toggle();
            });
    
            // Append the table to the body
            $('body').append($fixedTable);
        }

        function showPrices() {
            const newRow = `
                <tr>
                    <td>Le prix de la vitrine </td>
                    <td></td>
                    <td id="tr_resume_prix_de_capot">0 €</td>
                </tr>
                <tr>
                    <td>Le prix du socle </td>
                    <td></td>
                    <td id="tr_resume_prix_de_socle">0 €</td>
                </tr>
            `;
            $('#resume_tr_poids').after(newRow);
        }

        // titleHolderTitle();
        insertSvgContainer();

        if (!cube.on) {
            changePositions();
            appendUnits();
            topButtons();
            openFirst();
            textConfig();
            if(invirement && invirement === 'development') createCollapsibleTable2();
        } else {
            showPrices();
            openFirstCube();
            appendUnitsCube();
            topButtonsCube();
            changeDivsToUrls();
            if(invirement && invirement === 'development') createCollapsibleTable();
            $('.price-information').hide();
        
        }

        Extrac();
    }

    function reverseSelect(shape_id, type) {
        var shape = [];
        var allInputs = [];
        switch (type) {
            case 1:
                shape = shapesToDraw.find(s => s.id === shape_id);
                allInputs = inputsToDraw;
                break;
            case 2:
                shape = shapesToHole.find(s => s.id === shape_id);
                allInputs = inputsToHole;
                break;
            case 3:
                shape = shapesToCut.find(s => s.id === shape_id);
                allInputs = inputsToCut;
                break;

            default:
                break;
        }

        if (!shape) return;

        allInputs.forEach(fld => {
            let id = fld.split('_')[1];
            unsetElement(id);
        });

        shape.fields.forEach(field => {
            let srtt = '#' + field;
            var inputField = $(srtt);
            inputField.closest('.step_content').addClass("finished");
            var id_option = inputField?.attr('id')?.replace('text_', '');
            $('#js_icp_next_opt_' + id_option).click();
        });
    }

    function show2DView() {
        const svgContainer = document.getElementById('svgContainer');
        const productCover = document.querySelector('.product-cover');

        if (svgContainer && productCover) {
            svgContainer.classList.add('hidden');
            productCover.classList.remove('hidden');
        } else {
            console.error('Error: One of the elements not found');
        }
    }

    function showImages() {
        const svgContainer = document.getElementById('svgContainer');
        const productCover = document.querySelector('.product-cover');

        if (svgContainer && productCover) {
            svgContainer.classList.remove('hidden');
            productCover.classList.add('hidden');
        } else {
            console.error('Error: One of the elements not found');
        }
    }

    function addThumbnails(path, id) {

        const newOwlItem = document.createElement('div');
        newOwlItem.className = 'owl-item active';
        newOwlItem.style.width = '188.919px';
        newOwlItem.style.marginRight = '22px';
        newOwlItem.id = id;

        const thumbContainerLi = document.createElement('li');
        thumbContainerLi.className = 'thumb-container';

        const thumbnailImg = document.createElement('img');
        thumbnailImg.className = 'thumb js-thumb';
        thumbnailImg.src = path;
        thumbnailImg.setAttribute('data-image-medium-src', path);
        thumbnailImg.setAttribute('data-image-large-src', path);
        thumbnailImg.alt = "";
        thumbnailImg.title = "";
        thumbnailImg.width = 100;
        thumbnailImg.itemprop = 'image';

        thumbContainerLi.appendChild(thumbnailImg);

        newOwlItem.appendChild(thumbContainerLi);

        const owlStage = document.querySelector('.owl-stage-outer .owl-stage');

        if (owlStage) {
            owlStage.style.width = (parseFloat(owlStage.style.width) + 188.919 + 22) + 'px';
            owlStage.appendChild(newOwlItem);
        }
    }

    function setDefaultDraw() {
        setElementsToDraw.forEach(element => {
            setInputValue(element.id, element.default);
        });
    }

    function setDefaultHoles() {
        var radius = Math.min(width_cm, height_cm) / 3;
        var centerX = holesSettings.width / 2;
        var centerY = holesSettings.height / 2;

        const setElementsToHoles = [{
                id: 'text_18',
                default: 5
            },
            {
                id: 'text_21',
                default: 5
            },
            {
                id: 'text_22',
                default: 5
            },
            {
                id: 'text_23',
                default: 10
            },
            {
                id: 'text_25',
                default: parseFloat(centerX).toFixed(0)
            },
            {
                id: 'text_26',
                default: parseFloat(centerY).toFixed(0)
            },
            {
                id: 'text_27',
                default: parseFloat(radius).toFixed(0)
            },
            {
                id: 'text_28',
                default: 10
            }
        ];

        setElementsToHoles.forEach(element => {
            setInputValue(element.id, element.default);
        });
    }

    function setDefaultCut() {
        const { width, height } = cutSettings;
        
        if (!width || !height) {
            return;
        }
    
        const halfWidth = width / 2;
        const halfHeight = height / 2;
        const minDem = Math.min(halfWidth, halfHeight);
    
        const minDemHalf = (minDem / 2).toFixed(0);
        const minDemFifth = (minDem / 5).toFixed(0);
        const minDemQuarter = (minDem / 4).toFixed(0);
        const halfWidth70 = (halfWidth * 0.7).toFixed(0);
        const halfWidthThird = (halfWidth / 3).toFixed(0);
    
        const setElementsToCuts = [
            { id: 'text_64', default: minDemHalf },
            { id: 'text_65', default: minDemFifth },
            { id: 'text_63', default: halfWidth70 },
            { id: 'text_38', default: halfWidth.toFixed(0) },
            { id: 'text_39', default: halfWidthThird },
            { id: 'text_40', default: halfWidth.toFixed(0) },
            { id: 'text_41', default: halfHeight.toFixed(0) },
            { id: 'text_42', default: "0" },
            { id: 'text_113', default: "0" },
            { id: 'text_114', default: "0" },
            { id: 'text_115', default: "0" },
            { id: 'text_116', default: "0" },
            { id: 'text_45', default: minDemHalf },
            { id: 'text_66', default: minDemHalf },
            { id: 'text_67', default: minDemHalf },
            { id: 'text_68', default: minDemHalf },
            { id: 'text_69', default: minDemQuarter },
        ];
    
        setElementsToCuts.forEach(({ id, default: defaultValue }) => setInputValue(id, defaultValue));
    }
    
    function removeInputError(inputElement) {
        const inputGroupDiv = $(inputElement).closest('.card-block');
        inputGroupDiv.removeClass('input-section--invalid');
        inputGroupDiv.find('.measurements-selector__error').remove();
    }
    function getFormattedMessage(rawMessage, min, max) {
        return rawMessage
            .replace('${limits.min}', min)
            .replace('${limits.max}', max);
    }

    function displayError(inputElements, errorMessage) {
        var inputGroupDiv = $(inputElements).closest('.card-block');
        inputGroupDiv.find('.measurements-selector__error').remove();
        inputGroupDiv.addClass('input-section--invalid');

        let errorHtml = `<span class="measurements-selector__error">
            ${errorMessage}
        </span>`;

        inputGroupDiv.append(errorHtml);
    }

    function checkInputRange(inputId) {
        let valide = false;

        function validateArrowDimensions() {
            var tailW = getInputValue('text_53');
            var tailH = getInputValue('text_55');
            var headW = getInputValue('text_54');
            var headH = getInputValue('text_56');

            var totalWidth = tailW + headW;
            var totalHeight = 2 * headH + tailH;

            var widthIsValid = validateDimension(totalWidth, 50, 2050, '#text_53, #text_54', getFormattedMessage(idxr_tr_limits_range, 50, 2050));
            var heightIsValid = validateDimension(totalHeight, 50, 1540, '#text_55, #text_56', getFormattedMessage(idxr_tr_limits_range, 50, 1540));

            if (widthIsValid && heightIsValid) {
                reStart();
                valide = true;
            }
        }

        function validateDimension(dimension, min, max, selector, errorMessage) {
            if (dimension < min || dimension > max) {
                displayError(selector, errorMessage);
                return false;
            }
            return true;
        }

        let inputElement = $('#' + inputId);
        let inputValue = inputElement.val();
        let limits = inputLimits[inputId];
        
        let inputGroupDiv = inputElement.closest('.card-block');

        inputGroupDiv.find('.measurements-selector__error').remove();
        inputGroupDiv.removeClass('input-section--invalid');
        if (inputValue === '') {
            displayError('#' + inputId, idxr_tr_chemp_vide);
        } else if (limits) {
            // if (shapeSettings.type == 7 && (inputId == 'text_10' || inputId == 'text_3' ) && (getInputValue('text_10') < getInputValue('text_3')/2)) {
            //     displayError('#' + inputId, 'La hauteur doit être supérieure à la moitié de la largeur.');
            // } else 
            if (inputValue < limits.min || inputValue > limits.max) {
                // displayError('#' + inputId, `La valeur doit être entre ${limits.min} mm et ${limits.max} mm`);
                displayError('#' + inputId, getFormattedMessage(idxr_tr_limits_range, limits.min, limits.max));
            } else {
                if (!cube.on) reStart();
                else drawCube();
                valide = true;
            }
        } else if (shapeSettings.type == 10) {
            validateArrowDimensions();
        } else if (shapeSettings.type == 7) {
            validateArrowDimensions();
        } else {
            if (!cube.on) reStart();
            else drawCube();
            valide = true;
        }
        return valide;
    }

    const inputLimits = {
        'text_3': {
            min: getEdge('product_Largeur_min'),
            max: getEdge('product_Largeur_max')
        },
        'text_4': {
            min: getEdge('product_longueur_min'),
            max: getEdge('product_longueur_max')
        },
        'text_109': {
            min: 0,
            max: 1000
        },
        'text_110': {
            min: 0,
            max: 1000
        },
        'text_111': {
            min: 0,
            max: 1000
        },
        'text_112': {
            min: 0,
            max: 1000
        },
        'text_9': {
            min: getEdge('product_Largeur_min'),
            max: getEdge('product_Largeur_max')
        },
        'text_10': {
            min: getEdge('product_Largeur_min'),
            max: getEdge('product_Largeur_max')
        },
        'text_11': {
            min: getEdge('product_longueur_min'),
            max: getEdge('product_longueur_max')
        },
        'text_13': {
            min: 1,
            max: 100
        },
        'text_23': {
            min: 1,
            max: 100
        },
        'text_57': {
            min: getEdge('product_longueur_min'),
            max: getEdge('product_longueur_max')/2
        },
        'text_58': {
            min: getEdge('product_Largeur_min'),
            max: getEdge('product_Largeur_max')/2
        },
        'text_76': {
            min: 30,
            max: getEdge('product_Largeur_max')
        },
        'text_77': {
            min: 50,
            max: getEdge('product_longueur_max')
        },
        'text_78': {
            min: 50,
            max: parseFloat(get_max_height())
        },
        'text_87': {
            min: 15,
            max: parseFloat(get_max_height())
        },
    };
    
    function getEdge(txt){
        if (shapeSettings.type === 11) {
            return parseFloat(getInputValue(txt, 800))/2;
        }
        return parseFloat(getInputValue(txt, 800));
    }

    function get_max_height() {
        // Use jQuery to find the desired value
        const hauteurMaximale = $(".data-sheet .name")
            .filter(function() {
                return $(this).text().trim() === "Hauteur maximale (mm)";
            })
            .next(".value")  // Select the next <dd> sibling with the class 'value'
            .text()
            .trim();
            
        // Check if the value is empty, `0`, or not a number, and return `500` if so
        return hauteurMaximale && parseFloat(hauteurMaximale) !== 0 ? hauteurMaximale : "500";
    }

    function drawCube(start = false) {
        svg.clear();
        shapeGroup = svg.select('#shapeContainer');
        if (!shapeGroup) {
            shapeGroup = svg.group().attr({
                id: 'box'
            });
        }

        const width = getInputValue('text_76', 200);
        const heightBox = getInputValue('text_78', 200);
        const heightBase = getInputValue('text_87', 200);
        const depth = getInputValue('text_77', 200);
        const boxColor = cube.scoleColor;
        const height = heightBase + heightBox;
        // Validation function to check limits from inputLimits
        function isValide(inputId, value) {
            const limits = inputLimits[inputId];
            if (limits) {
                const { min, max } = limits;
                return value >= min && value <= max;
            }
            return false;
        }
        if (
            !isValide('text_76', width) ||
            !isValide('text_78', heightBox) ||
            !isValide('text_77', depth)
        ) {return;}

        cube.socleHeight = heightBase;
        setDemensions(width, heightBox, depth, idxr_tr_custom_display);

        if (cube.faces === 4) {
            perimeter = 2 * heightBox + width + 2 * depth;
        } else {
            perimeter = 4 * heightBox + 2 * width + 2 * depth;
        }

        const largestDimension = Math.max(width, height, depth);
        const cubeScaleFactor = 200 / largestDimension;

        var containerWidth = $('#svgContainer').width();
        var containerHeight = $('#svgContainer').height();

        var targetSize = 400;
        var largestContainerDimension = Math.max(containerWidth, containerHeight);
        if (largestContainerDimension < 1) largestContainerDimension = targetSize;
        var containerScaleFactor = targetSize / largestContainerDimension;
        var containerWidthScaled = containerWidth * containerScaleFactor;
        var containerHeightScaled = containerHeight * containerScaleFactor;

        const scaledWidth = width * cubeScaleFactor;
        const scaledHeightBox = heightBox * cubeScaleFactor;
        const scaledHeightBase = heightBase * cubeScaleFactor;
        const scaledDepth = depth * cubeScaleFactor;

        try {
            try {
                // Calculate each value safely to ensure non-negative values
                const x = Math.max(0, containerWidthScaled / 2 - scaledWidth / 2 - 40);
                const y = Math.max(0, containerHeightScaled / 2 - (scaledHeightBase + scaledHeightBox) / 2 - 40);
                const width = Math.max(0, containerWidthScaled - 20);
                const height = Math.max(0, containerHeightScaled - 20);

                // Set the viewBox attribute with safe values
                svg.attr("viewBox", `-${x} -${y} ${width} ${height}`);
            } catch (error) {
                console.error('it"s just ViewBox error!');
            }
            drawBoxes(scaledWidth, scaledHeightBox, scaledHeightBase, scaledDepth);
            addDimensionArrows(scaledWidth, scaledHeightBox, scaledHeightBase, scaledDepth, width, heightBox, heightBase, depth);
            if (!start) showImages();
        } catch (error) {
            console.info(error);
        }

        calculateWeight();
        if (typeof updateTotale !== 'undefined') {
            updateTotale();
        }

        function addDimensionArrows(width, height, height2, depth, scaledWidth, scaledHeight2, scaledHeight, scaledDepth) {

            var adjacent = depth * Math.cos(45 * (Math.PI / 180));
            var hypo = depth * Math.sin(45 * (Math.PI / 180));

            const startX = 0;
            const startY = adjacent / 2;
            const startY2 = startY + height2;

            svg.line(
                startX,
                startY2 + height + 20,
                startX + width,
                startY2 + height + 20
            ).attr(arrowsHeads());

            svg.line(startX - 20, startY, startX - 20, startY + height).attr(arrowsHeads());

            if (cube.base) svg.line(startX - 20, startY + height, startX - 20, startY2 + height).attr(arrowsHeads());

            svg.line(
                startX + width,
                startY2 + height + 20,
                startX + width + hypo / 2 + 20,
                startY2 + height + 20 - adjacent / 2 - 20,
            ).attr(arrowsHeads());

            addText('L : ', `${Math.round(scaledWidth)} mm`, startX + width / 2, startY2 + height + 38, horText);

            addText('H.B : ', `${Math.round(scaledHeight2)} mm`, startX - 25, startY + height / 2, horText, 'vertical', startX - 25, startY + height / 2);

            if (cube.base) addText('H.S: ', `${Math.round(scaledHeight)} mm`, startX - 25, startY + height + height2 / 2, horText, 'vertical', startX - 25, startY + height + height2 / 2);

            addText('P : ', `${Math.round(scaledDepth)} mm`, startX + width + 20, startY2 + height + 20, horText, 'depth', startX + width, startY2 + height - 20);
        }

        function drawBoxes(width, height1, height2, depth) {
            shapeGroup.clear();

            var adjacent = depth * Math.cos(45 * (Math.PI / 180));

            const startX = 0;
            const startY1 = adjacent / 2;
            const startY2 = startY1 + height1;

            const backGradientTop = svg.gradient("l(0, 0, 1, 1)rgba(153, 192, 255, 0.7)-rgba(204, 230, 255, 0.7)");
            const bottomGradientTop = svg.gradient("l(0, 0, 1, 1)rgba(153, 192, 255, 0.5)-rgba(204, 230, 255, 0.5)");
            const leftGradientTop = svg.gradient("l(0, 0, 1, 1)rgba(179, 209, 255, 0.5)-rgba(230, 242, 255, 0.5)");
            const rightGradientTop = svg.gradient("l(0, 0, 1, 1)rgba(179, 209, 255, 0.5)-rgba(230, 242, 255, 0.5)");
            const frontGradientTop = svg.gradient("l(0, 0, 1, 1)rgba(204, 230, 255, 0.5)-rgba(242, 249, 255, 0.5)");

            function adjustColor(color, percent) {
                const num = parseInt(color.slice(1), 16),
                    amt = Math.round(2.55 * percent),
                    R = (num >> 16) + amt,
                    G = (num >> 8 & 0x00FF) + amt,
                    B = (num & 0x0000FF) + amt;

                return `#${(0x1000000 + (R < 255 ? (R < 1 ? 0 : R) : 255) * 0x10000 + 
                            (G < 255 ? (G < 1 ? 0 : G) : 255) * 0x100 + 
                            (B < 255 ? (B < 1 ? 0 : B) : 255)).toString(16).slice(1).toUpperCase()}`;
            }

            function createGradient(boxColor) {
                const adjustedColor = boxColor === "#000000" ? adjustColor("#a2a2a2", -20) : adjustColor(boxColor, -20);
                return `l(0, 0, 1, 1)${boxColor}-${adjustedColor}`;
            }

            const backGradientBottom = svg.gradient(createGradient(boxColor));
            const bottomGradientBottom = svg.gradient(createGradient(boxColor));
            const leftGradientBottom = svg.gradient(createGradient(adjustColor(boxColor, -10)));
            const rightGradientBottom = svg.gradient(createGradient(adjustColor(boxColor, -10)));
            const frontGradientBottom = svg.gradient(createGradient(adjustColor(boxColor, -30)));
            const topGradientBottom = svg.gradient(createGradient(adjustColor(boxColor, -15)));

            function drawBox(startY, height, gradients, first, stroke, hasFourFaces) {

                shapeGroup.add(svg.path(`M${startX + depth/2},${startY - depth/2} L${startX + width + depth/2},${startY - depth/2} L${startX + width + depth/2},${startY + height - depth/2} L${startX + depth/2},${startY + height - depth/2} Z`).attr({
                    fill: gradients.back,
                    stroke: stroke
                }));

                shapeGroup.add(svg.path(`M${startX},${startY} L${startX + depth/2},${startY - depth/2} L${startX + depth/2},${startY + height - depth/2} L${startX},${startY + height} Z`).attr({
                    fill: gradients.left,
                    stroke: stroke
                }));

                if (hasFourFaces || !first) shapeGroup.add(svg.path(`M${startX + width},${startY} L${startX + width + depth/2},${startY - depth/2} L${startX + width + depth/2},${startY + height - depth/2} L${startX + width},${startY + height} Z`).attr({
                    fill: gradients.right,
                    stroke: stroke
                }));

                shapeGroup.add(svg.rect(startX, startY, width, height).attr({
                    fill: gradients.front,
                    stroke: stroke
                }));

                if (first && (cube.faces === 6)) {

                    shapeGroup.circle(startX + 8, startY + height - 8, 5).attr({
                        fill: 'white',
                        stroke: 'gray',
                        strokeWidth: 1,
                    });
                    shapeGroup.circle(startX + width - 8, startY + height - 8, 5).attr({
                        fill: 'white',
                        stroke: 'gray',
                        strokeWidth: 1,
                    });
                    shapeGroup.circle(startX + 8, startY + 8, 5).attr({
                        fill: 'white',
                        stroke: 'gray',
                        strokeWidth: 1,
                    });
                    shapeGroup.circle(startX + width - 8, startY + 8, 5).attr({
                        fill: 'white',
                        stroke: 'gray',
                        strokeWidth: 1,
                    });
                }

                shapeGroup.add(svg.path(`M${startX},${startY} L${startX + depth/2},${startY - depth/2} L${startX + width + depth/2},${startY - depth/2} L${startX + width},${startY} Z`).attr({
                    fill: gradients.bottom,
                    stroke: 'none'
                }));
            }

            if(cube.scoleColor === 'lightblue'){
                drawBox(startY2, height2, {
                    back: backGradientTop,
                    bottom: bottomGradientTop,
                    left: leftGradientTop,
                    right: rightGradientTop,
                    front: frontGradientTop
                }, false, 'lightblue', false);
            }else{
                drawBox(startY2, height2, {
                    back: backGradientBottom,
                    bottom: bottomGradientBottom,
                    left: leftGradientBottom,
                    right: rightGradientBottom,
                    front: frontGradientBottom,
                    top: topGradientBottom
                }, false, 'none', false);
            }

            drawBox(startY1, height1, {
                back: backGradientTop,
                bottom: bottomGradientTop,
                left: leftGradientTop,
                right: rightGradientTop,
                front: frontGradientTop
            }, true, 'lightblue', cube.faces !== 4);

        }
    }

    function getCardLabel(cardId) {
        const label = $('#' + cardId).find('label.option_titles').first();
        return label.length ? label.text().trim() : null;
    }

    function drawShape() { 
        svg = Snap("#actualSvg");
        shapeGroup = svg.select('#shapeContainer');
        const arrowsGroup = svg.select('#arrowsContainer');
        const holesGroup = svg.select('#holesContainer');
        const cutoutGroup = svg.select('#cutoutContainer');
        const cutoutDems = svg.select('#cutoutDems');
        shapeGroup.clear();
        arrowsGroup.clear();
        holesGroup.clear();
        cutoutGroup.clear();
        cutoutDems.clear();
        var shaper = shapeGroup;
        var arrows = arrowsGroup;
        function copySvgOnly() {
            // Clone the SVG element with id="actualSvg"
            let svgClone = $('#svgContainer #actualSvg').clone();
        
            // Clear the content of the target elements and append the cloned SVG
            $('#step_2_preview, #step_17_preview, #step_29_preview').empty().append(svgClone.clone());
        }
        
        var extraInfo = drow(shapeSettings.type);
        holes(holesSettings.type, extraInfo);
        cut(cutSettings.type);
        copySvgOnly();

        function calculateScaleFactor(dimension, target = 400) {
            const maxDimension = Math.max(...dimension);
            const targetSize = target;
            return maxDimension == 0 ? 1 : targetSize / maxDimension;
        }

        function mainAttrs(type = 1) {
            if (type == 2) return {
                fill: '#e2ffc1',
                stroke: "#065075"
            };
            return {
                fill: '#F0FAFF',
                stroke: "#065075",
                id: 'shapeHolder'
            };
        };

        function scale(num) {
            return parseFloat(num) * scaleFactor;
        }
        function unScale(num) {
            if (scaleFactor === 0) {
                throw new Error("scaleFactor cannot be zero");
            }
            // Perform unscaling and round to avoid precision issues
            const unscaledValue = parseFloat(num) / scaleFactor;
            return parseFloat(unscaledValue.toFixed(2));
        }
        
        function drow(type) {
            var extraInfo = 0;
            switch (type) {
                case 1:
                    var drawWidth = getValue('text_3', 200);
                    var drawHeight = getValue('text_4', 100);
                    var drawRadius1 = getValue('text_109',0);
                    var drawRadius2 = getValue('text_110', 0);
                    var drawRadius3 = getValue('text_111', 0);
                    var drawRadius4 = getValue('text_112', 0);
                    scaleFactor = calculateScaleFactor([drawWidth, drawHeight]);
                    rect_draw(drawWidth, drawHeight, drawRadius1, drawRadius2, drawRadius3, drawRadius4);
                    setDemensions(drawWidth, drawHeight, 0, getCardLabel('card_31_0'));
                    perimeter = (drawWidth + drawHeight) * 2;
                    break;
                case 2:
                    var drawRadius = getValue('text_9', 200);
                    scaleFactor = calculateScaleFactor([drawRadius]);
                    circle(drawRadius);
                    setDemensions(drawRadius, drawRadius, 0,  getCardLabel('card_31_1'));
                    perimeter = Math.PI * drawRadius;
                    break;
                case 3:
                    var drawRadius = getValue('text_9', 40);
                    scaleFactor = calculateScaleFactor([drawRadius]);
                    halfCircle(drawRadius);
                    setDemensions(drawRadius, drawRadius / 2, 0, getCardLabel('card_31_12'));
                    perimeter = (Math.PI * drawRadius / 2) + drawRadius;
                    break;
                case 4:
                    var drawWidth = getValue('text_3', 150);
                    var drawHeight = getValue('text_4', 100);

                    scaleFactor = calculateScaleFactor([drawWidth, drawHeight]);
                    ellipse(drawWidth, drawHeight);
                    setDemensions(drawWidth, drawHeight, 0, getCardLabel('card_31_2'));
                    let a = drawWidth / 2;
                    let b = drawHeight / 2;
                    perimeter = Math.PI * (3 * (a + b) - Math.sqrt((3 * a + b) * (a + 3 * b)));
                    break;
                case 5:
                    var drawWidth = getValue('text_3', 150);
                    var drawHeight = getValue('text_4', 300);
                    var drawDepth = getValue('text_10', 200);
                    scaleFactor = calculateScaleFactor([drawWidth, drawHeight, drawDepth]);
                    trapezoidalRight(drawWidth, drawHeight, drawDepth);

                    perimeter = drawWidth + drawHeight + Math.sqrt(Math.pow(drawDepth, 2) + Math.pow((drawWidth - drawHeight) / 2, 2)) * 2;
                    setDemensions(Math.max(drawWidth, drawDepth), drawHeight, 0,  getCardLabel('card_31_3'));
                    break;
                case 6:
                    var drawWidth = getValue('text_3', 200);
                    var drawHeight = getValue('text_4', 250);
                    var drawDepth = getValue('text_10', 300);
                    scaleFactor = calculateScaleFactor([drawHeight, Math.max( drawWidth, drawDepth )]);
                    trapezoidalIsosceles(drawWidth, drawDepth, drawHeight );

                    perimeter = drawWidth + drawHeight + Math.sqrt(Math.pow(drawDepth, 2) + Math.pow((drawWidth - drawHeight) / 2, 2)) * 2;
                    setDemensions(Math.max(drawWidth, drawDepth), drawHeight, 0, getCardLabel('card_31_13'));
                    break;
                case 7:
                    var drawWidth = getValue('text_3', 160);
                    var drawHeight = getValue('text_10', 200);

                    scaleFactor = calculateScaleFactor([drawWidth, drawHeight]);
                    door(drawWidth, drawHeight);
                    setDemensions(drawWidth, drawHeight, 0, getCardLabel('card_31_4'));
                    var __radius = drawWidth / 2;
                    var __rectHeight = drawHeight - __radius;
                    perimeter = 2 * (drawWidth + __rectHeight) + (Math.PI * __radius);
                    break;
                case 8:
                case 9:
                case 10:
                    var drawWidth = getValue('text_10', 260);
                    var drowBase = getValue('text_11', 200);

                    scaleFactor = calculateScaleFactor([drawWidth, drowBase]);
                    var triangleArg = (type === 10) ? 1 : (type === 9) ? 0 : undefined;
                    triangle(drowBase, drawWidth, triangleArg);
                    perimeter = drowBase + drawWidth + Math.sqrt(drowBase * drowBase + drawWidth * drawWidth);

                    setDemensions(drowBase, drawWidth, 0, getCardLabel('card_31_5'));
                    break;
                case 11:
                    var drawWidth = getValue('text_9', 150);
                    scaleFactor = calculateScaleFactor([drawWidth], 200);
                    hexagon(drawWidth);
                    setDemensions(drawWidth * 2, drawWidth * Math.sqrt(3), 0, getCardLabel('card_31_8'));
                    perimeter = drawWidth * 6;
                    extraInfo = drawWidth;
                    break;
                case 12:
                    let arrowTailWidth = getValue('text_53', 200);
                    let arrowHeadWidth = getValue('text_54', 200);
                    let arrowTailHeight = getValue('text_55', 220);
                    let arrowHeadHeight = getValue('text_56', 120);

                    scaleFactor = calculateScaleFactor([arrowTailWidth + arrowHeadWidth, arrowTailHeight + 2 * arrowHeadHeight]);
                    arrow(arrowTailWidth, arrowHeadWidth, arrowTailHeight, arrowHeadHeight);

                    setDemensions(arrowTailWidth + arrowHeadWidth, arrowTailHeight + (2 * arrowHeadHeight), 0,  getCardLabel('card_31_9'));
                    let tailP = (arrowTailWidth + arrowTailHeight) * 2;
                    let headP = arrowHeadWidth + arrowHeadHeight + Math.sqrt(arrowHeadWidth * arrowHeadWidth + arrowHeadHeight * arrowHeadHeight);

                    perimeter = tailP + headP - arrowTailHeight;
                    break;
                case 13:
                    let outerRadiusStar = getValue('text_57', 100);
                    let innerRadiusStar = getValue('text_58', 40);
                    let pointsStar = getValue('text_13', 5);

                    scaleFactor = calculateScaleFactor([outerRadiusStar, innerRadiusStar]);
                    star(outerRadiusStar, innerRadiusStar, pointsStar);

                    setDemensions(outerRadiusStar * 2, outerRadiusStar * 2, 0, getCardLabel('card_31_11'));
                    let angleRad = Math.PI / pointsStar;
                    let segmentLength = Math.sqrt(Math.pow(outerRadiusStar, 2) + Math.pow(innerRadiusStar, 2) - 2 * outerRadiusStar * innerRadiusStar * Math.cos(angleRad));
                    perimeter = segmentLength * 2 * pointsStar;

                    break;
                case 14:
                    var textInput = $('#text_6').val() || 'Louis';
                    var textFont = $('#text_81').val() || '/modules/idxrcustomproduct/views/js/fonts/Alfa_Slab_One/AlfaSlabOne-Regular.ttf';
                    var textDem = getValue('text_83', 200);
                    scaleFactor = calculateScaleFactor([textDem]);
                    text(textInput, textFont, textDem);

                    break;
                case 15:
                    var drawWidth = preDecoupSetting.width;
                    var drawHeight = preDecoupSetting.height;
                    scaleFactor = calculateScaleFactor([drawWidth, drawHeight]);
                    rect(drawWidth, drawHeight, 0);
                    setDemensions(drawWidth, drawHeight, 0, getCardLabel('card_61_1')); // predecoupe
                    perimeter = (drawWidth + drawHeight) * 2;
                    break;
                case 16:
                    var drawWidth = getValue('text_3', 100);
                    var drawHeight = getValue('text_4', 200);

                    scaleFactor = calculateScaleFactor([drawWidth, drawHeight]);
                    egg(drawWidth, drawHeight);
                    setDemensions(drawWidth, drawHeight, 0, getCardLabel('card_31_0'));
                    let cc = drawWidth / 2;
                    let dd = drawHeight / 2;
                    perimeter = Math.PI * (3 * (cc + dd) - Math.sqrt((3 * cc + dd) * (cc + 3 * dd)));
                    break;
            }
            return extraInfo;
        }

        function holes(type = 0, extraInfo = 0) {
            var out_type = shapeSettings.type;
            const holeAttrs = {
                class: 'hole',
                fill: '#FFFFFF',
                stroke: "#065075"
            };
            var extra = 0;
            if (shapeSettings.type == 12) {
                extra = scale(getValue('text_56', 120));
            }

            switch (type) {
                case 0:
                    // nothing
                    break;
                case 1:
                    var rows = getValue('text_21', 5);
                    var cols = getValue('text_22', 5);
                    var radius = scale(getValue('text_18', 10));
                    var padding = scale(getValue('text_23', 10));
                    if((out_type === 8) || (out_type === 9) || (out_type === 10)) 
                        drawTriangleBorderHoles(rows, cols, padding, radius, extra, out_type);
                    else if(out_type === 11) drawHexagonBorderHoles(rows, cols, padding, radius, extra, extraInfo);
                    else drawBorderHoles(rows, cols, 2*padding, radius, extra);
                    break;
                case 2:
                    var numHoles = getValue('text_28', 8);
                    var holeRadius = scale(getValue('text_18', 10));
                    var radiusCircle = scale(getValue('text_27', 25));
                    var centerCircleX = scale(getValue('text_25', 40));
                    var centerCircleY = scale(getValue('text_26', 40));

                    drawRadialHoles(centerCircleX, centerCircleY, radiusCircle, numHoles, holeRadius, extra);
                    break;
                case 3:
                    var rows = getValue('text_21', 5);
                    var cols = getValue('text_22', 6);
                    var radius = scale(getValue('text_18', 5));
                    var padding = scale(getValue('text_23', 10));

                    drawGridHoles(rows, cols, padding, radius, extra);
                    break;
                case 4:
                    drawFreeHole(holesSettings.width, holesSettings.height, extra);
                    break;
                default:
                    console.error("Invalid hole type");
            }

            function drawGridHoles(rows, cols, padding, holeRadius, extra) {

                const spacingX = (scale(holesSettings.width) - 2 * padding) / (cols - 1);
                const spacingY = (scale(holesSettings.height) - 2 * padding) / (rows - 1);

                for (let i = 0; i < rows; i++) {
                    for (let j = 0; j < cols; j++) {
                        const cx = padding + spacingX * j;
                        const cy = padding + spacingY * i - extra;
                        if((j===0) && (i===0)) holeDems(cx, padding - extra, radius, extra, 0, padding);
                        holesGroup.circle(cx, cy, holeRadius).attr(holeAttrs);
                    }
                }
            }
            
            function drawBorderHoles(rows, cols, padding, radius, extra) {
                // Draw a single hole
                if (rows === 1 && cols === 1) {
                    const cx = padding;
                    const cy = padding - extra;
                    holesGroup.circle(cx, cy, radius).attr(holeAttrs);
                    holeDems(cx, padding - extra, radius, extra, 0, padding/2);
                    return;
                }
                // Handle single row or single column cases
                if (rows === 1 || cols === 1) {
                    // Single row of holes
                    if (rows === 1) {
                        const spacingX = (scale(holesSettings.width) - 2 * padding) / (cols - 1);
                        for (let j = 0; j < cols; j++) {
                            const cx = padding + spacingX * j;
                            if (j === 0) holeDems(cx, padding - extra, radius, extra, 0, padding/2);
                            holesGroup.circle(cx, padding - extra, radius).attr(holeAttrs);
                        }
                    }
                    
                    // Single column of holes
                    if (cols === 1) {
                        const spacingY = (scale(holesSettings.height) - 2 * padding) / (rows - 1);
                        for (let i = 0; i < rows; i++) {
                            const cy = padding + spacingY * i - extra;
                            if (i === 0) holeDems(cy, padding - extra, radius, extra, 0, padding/2);
                            holesGroup.circle(padding, cy, radius).attr(holeAttrs);
                            
                        }
                    }
                    return; // Exit after handling single row or column cases
                }
                
                // Calculate spacing for multiple rows and columns
                const spacingX = (scale(holesSettings.width) - 2 * padding) / (cols - 1);
                const spacingY = (scale(holesSettings.height) - 2 * padding) / (rows - 1);
            
                // Top and Bottom Border
                for (let j = 0; j < cols; j++) {
                    const cx = padding + spacingX * j;
                    if (j === 0) holeDems(cx, padding - extra, radius, extra, 0, padding/2);
                    holesGroup.circle(cx, padding - extra, radius).attr(holeAttrs); // Top row
                    holesGroup.circle(cx, scale(holesSettings.height) - padding - extra, radius).attr(holeAttrs); // Bottom row
                }
            
                // Left and Right Border
                for (let i = 1; i < rows - 1; i++) {
                    const cy = padding + spacingY * i;
                    holesGroup.circle(padding, cy - extra, radius).attr(holeAttrs); // Left column
                    holesGroup.circle(scale(holesSettings.width) - padding, cy - extra, radius).attr(holeAttrs); // Right column
                }
            }

            function drawRadialHoles(centerCircleX, centerCircleY, radius, numHoles, holeRadius, extra) {
                // Handle case where only one hole is drawn at the center
                if (numHoles === 1) {
                    holesGroup.circle(centerCircleX, centerCircleY - extra, holeRadius).attr(holeAttrs);
                    return;
                }
            
                // Define the angle step for positioning each hole around the circle
                const angleStep = (2 * Math.PI) / numHoles;
            
                // Loop through each hole position
                for (let i = 0; i < numHoles; i++) {
                    const angle = i * angleStep;
                    const cx = centerCircleX + radius * Math.cos(angle);
                    const cy = centerCircleY + radius * Math.sin(angle) - extra;
            
                    // Draw dimensions if it’s the first hole (i.e., at index 0)
                    if (i === 0) {
                        holeDems(cx, cy, holeRadius, extra, radius, 0, centerCircleX, centerCircleY);
                    }
            
                    // Draw each hole at calculated positions
                    holesGroup.circle(cx, cy, holeRadius).attr(holeAttrs);
                }
            }
            
            function drawFreeHole(shapeWidth, shapeHeight, x = shapeWidth / 2, y = shapeHeight / 2, extra) {
                holesGroup.circle(x, y, 5).attr(holeAttrs);
            }
            
            function calculateAngle(pointA, pointB, pointC) {
                // Extract coordinates
                const [x1, y1] = pointA; // Coordinates of Point A
                const [x2, y2] = pointB; // Coordinates of Point B (vertex)
                const [x3, y3] = pointC; // Coordinates of Point C
            
                // Calculate vectors BA and BC
                const vectorBA = [x1 - x2, y1 - y2];
                const vectorBC = [x3 - x2, y3 - y2];
            
                // Calculate the dot product of vectors BA and BC
                const dotProduct = vectorBA[0] * vectorBC[0] + vectorBA[1] * vectorBC[1];
            
                // Calculate the magnitudes of vectors BA and BC
                const magnitudeBA = Math.sqrt(vectorBA[0] ** 2 + vectorBA[1] ** 2);
                const magnitudeBC = Math.sqrt(vectorBC[0] ** 2 + vectorBC[1] ** 2);
            
                // Calculate the cosine of the angle
                const cosTheta = dotProduct / (magnitudeBA * magnitudeBC);
            
                // Calculate the angle in radians and then convert to degrees
                const angleRadians = Math.acos(cosTheta);
                // const angleDegrees = (angleRadians * 180) / Math.PI;
            
                return angleRadians;
            }

            function drawTriangleBorderHoles(rows, cols, padding, holeRadius, extra, type = 8) {
                // Positions of the 3 corners of the triangle
                var scalledHeight = scale(holesSettings.height);
                var scalledWidth = scale(holesSettings.width);
            
                var topType = [scalledWidth / 2, scalledHeight / 2];
                var topX = scalledWidth / 2;
                if (type === 9) { topX = padding; topType = [0, 0]; }
                else if (type === 10) { topType = [scalledWidth, 0]; topX = scalledWidth - padding; }
            
                var angle0 = calculateAngle(
                    [scalledWidth, scalledHeight],
                    topType,
                    [0, scalledHeight],
                );
                // sin{a2} = padding / x
                var paddingY = padding;
                if (Math.abs(Math.sin(angle0 / 2)) > Number.EPSILON) {
                    paddingY = Math.abs(padding / Math.sin(angle0 / 2));
                }
                var angle1 = calculateAngle(
                    topType,
                    [0, scalledHeight],
                    [scalledWidth, scalledHeight],
                );
                // sin{a} = padding / x
                var paddingX = padding;
                var ang1 = (type === 8) ? angle1 : angle1 / 2;
                if (Math.abs(Math.sin(ang1)) > Number.EPSILON && (type !== 9)) {
                    paddingX = Math.abs(padding / Math.sin(ang1));
                }
                var angle2 = calculateAngle(
                    topType,
                    [scalledWidth, scalledHeight],
                    [0, scalledHeight],
                );
                // sin{a2} = padding / x
                var paddingZ = padding;
                var ang2 = (type === 8) ? angle2 : angle2 / 2;
                if (Math.abs(Math.sin(ang2)) > Number.EPSILON && (type !== 10)) {
                    paddingZ = Math.abs(padding / Math.sin(ang2));
                }
            
                const topY = paddingY;
                const bottomLeftX = paddingX;
                const bottomLeftY = scalledHeight - padding;
                const bottomRightX = scalledWidth - paddingZ;
                const bottomRightY = scalledHeight - padding;
            
                // Handle single hole case
                if (rows === 1 && cols === 1) {
                    const cx = topX;
                    const cy = topY - extra;
                    holeDems(cx, cy, holeRadius, extra, 0, padding);
                    holesGroup.circle(cx, cy, holeRadius).attr(holeAttrs);
                    return;
                }
            
                // Handle single row case
                if (rows === 1) {
                    // Draw holes along the top to bottom-right edge
                    for (let i = 0; i < cols; i++) {
                        const cx = topX + (bottomRightX - topX) * (i / (cols - 1));
                        const cy = topY + (bottomRightY - topY) * (i / (cols - 1));
                        if (i === 0) holeDems(cx, cy - extra, holeRadius, extra, 0, padding);
                        holesGroup.circle(cx, cy - extra, holeRadius).attr(holeAttrs);
                    }
                    return;
                }
            
                // Handle single column case
                if (cols === 1) {
                    // Draw holes along the top to bottom-left edge
                    for (let i = 0; i < rows; i++) {
                        const cx = topX - (topX - bottomLeftX) * (i / (rows - 1));
                        const cy = topY + (bottomLeftY - topY) * (i / (rows - 1));
                        if (i === 0) holeDems(cx, cy - extra, holeRadius, extra, 0, padding);
                        holesGroup.circle(cx, cy - extra, holeRadius).attr(holeAttrs);
                    }
                    return;
                }
            
                // Handle multiple rows and columns
                // Top to bottom-left edge
                for (let i = 0; i < cols; i++) {
                    const cx = topX - (topX - bottomLeftX) * (i / (cols - 1));
                    const cy = topY + (bottomLeftY - topY) * (i / (cols - 1));
                    if (i === 0) holeDems(cx, cy - extra, holeRadius, extra, 0, padding);
                    holesGroup.circle(cx, cy - extra, holeRadius).attr(holeAttrs);
                }
            
                // Bottom-left to bottom-right edge
                for (let i = 0; i < rows; i++) {
                    const cx = bottomLeftX + (bottomRightX - bottomLeftX) * (i / (rows - 1));
                    const cy = bottomLeftY + (bottomRightY - bottomLeftY) * (i / (rows - 1));
                    holesGroup.circle(cx, cy - extra, holeRadius).attr(holeAttrs);
                }
            
                // Top to bottom-right edge
                for (let i = 0; i < cols; i++) {
                    const cx = topX + (bottomRightX - topX) * (i / (cols - 1));
                    const cy = topY + (bottomRightY - topY) * (i / (cols - 1));
                    holesGroup.circle(cx, cy - extra, holeRadius).attr(holeAttrs);
                }
            }
            
            function drawHexagonBorderHoles(rows, cols, padding, holeRadius, extra, sideLength = 0, type = 1, xx = 0, yy = 0) {
                const scaledSideLength = scale(sideLength);
                const scaledHeight = scaledSideLength * Math.sqrt(3);

                let startX = xx;
                let startY = yy;
            
                if (type === 2) {
                    startX = xx - scaledSideLength;
                    startY = yy - scaledHeight / 2;
                }
            
                // Define the 6 vertices of the hexagon (after adjusting for padding)
                const hexagonVertices = [
                    [startX + scaledSideLength / 2 + padding, startY  + padding],
                    [startX + scaledSideLength * 1.5 - padding, startY + padding],
                    [startX + scaledSideLength * 2 - padding, startY + scaledHeight / 2 ],
                    [startX + scaledSideLength * 1.5 - padding, startY + scaledHeight - padding],
                    [startX + scaledSideLength / 2 + padding, startY + scaledHeight - padding],
                    [startX + padding, startY + scaledHeight / 2]
                ];
                var counter = 0;
                // Function to draw holes along an edge (between two vertices)
                function drawHolesAlongEdge(startVertex, endVertex, numHoles) {
                    for (let i = 0; i < numHoles; i++) {
                        const t = (numHoles > 1) ?  i / (numHoles - 1) : 1;
                        const cx = startVertex[0] + (endVertex[0] - startVertex[0]) * t;
                        const cy = startVertex[1] + (endVertex[1] - startVertex[1]) * t;
            
                        // Add the padding to the hole positions to create space around the holes
                        holesGroup.circle(cx, cy - extra, holeRadius).attr(holeAttrs);
                        if ((i === 0) && (counter === 0)){
                            holeDems(cx, padding - extra, radius, extra, 0, padding);
                            counter = 1;
                        }
                    }
                }
            
                // Draw holes along the 6 edges of the hexagon
                for (let i = 0; i < 6; i++) {
                    const startVertex = hexagonVertices[i];
                    const endVertex = hexagonVertices[(i + 1) % 6];  // Wrap around to the first vertex
            
                    // Alternate between cols and rows for the number of holes per edge
                    const numHoles = ((i % 3) === 0) ? cols : rows;
                    drawHolesAlongEdge(startVertex, endVertex, numHoles);
                }
            
            }
            
        }

        function cut(type = 12) {
            shaper = cutoutGroup;
            arrows = cutoutDems;
            var extra = 0;
            if (shapeSettings.type == 12) {
                extra = scale(getValue('text_56', 80));
            }
            var demX = getValue('text_40', 200);
            var demY = getValue('text_41', 200);
            var cutoutX = scale(demX);
            var cutoutY = scale(demY) - extra;
            var withArrows = true;
            switch (type) {
                case 1:
                        
                var drawRadius1 = getValue('text_113',0);
                var drawRadius2 = getValue('text_114', 0);
                var drawRadius3 = getValue('text_115', 0);
                var drawRadius4 = getValue('text_116', 0);
                var cutWidth = getValue('text_38', 700);
                var cutHeight = getValue('text_39', 500);
                
                rect_draw(cutWidth, cutHeight, drawRadius1, drawRadius2, drawRadius3, drawRadius4, 2, cutoutX, cutoutY);
                
                // var cutRadius = getValue('text_75', 0);
                // rect(cutWidth, cutHeight, cutRadius, 2, cutoutX, cutoutY);
                // perimeter2 = (cutWidth + cutHeight) * 2;
                    perimeter2 = (cutWidth + cutHeight) * 2;
                    break;
                case 2:
                    var cutRadiusCircle = getValue('text_45', 30);
                    circle(cutRadiusCircle, 2, cutoutX, cutoutY);
                    perimeter2 = Math.PI * cutRadiusCircle;
                    break;
                case 3:
                    var cutRadiusCircle = getValue('text_45', 50);
                    halfCircle(cutRadiusCircle, 2, cutoutX, cutoutY);
                    perimeter2 = (Math.PI * cutRadiusCircle / 2) + cutRadiusCircle;
                    break;
                case 4:
                    var cutWidth = getValue('text_38', 20);
                    var cutHeight = getValue('text_39', 40);

                    ellipse(cutWidth, cutHeight, 2, cutoutX, cutoutY);
                    let a = cutWidth / 2;
                    let b = cutHeight / 2;
                    perimeter2 = Math.PI * (3 * (a + b) - Math.sqrt((3 * a + b) * (a + 3 * b)));
                    break;
                case 5:
                    var cutTopWidth = getValue('text_63', 30);
                    var cutHeight = getValue('text_38', 20);
                    var cutWidth = getValue('text_39', 40);

                    trapezoidalRight(cutTopWidth, cutWidth, cutHeight, 2, cutoutX, cutoutY);

                    perimeter2 = cutTopWidth + cutHeight + Math.sqrt(Math.pow(cutWidth, 2) + Math.pow((cutTopWidth - cutHeight) / 2, 2)) * 2;
                    break;
                case 6:
                    var cutTopWidth = getValue('text_63', 30);
                    var cutHeight = getValue('text_38', 20);
                    var cutWidth = getValue('text_39', 40);

                    trapezoidalIsosceles(cutTopWidth, cutWidth, cutHeight, 2, cutoutX, cutoutY);

                    perimeter2 = cutTopWidth + cutHeight + Math.sqrt(Math.pow(cutWidth, 2) + Math.pow((cutTopWidth - cutHeight) / 2, 2)) * 2;
                    break;
                case 7:
                    var cutWidth = getValue('text_38', 20);
                    var cutHeight = getValue('text_39', 40);

                    door(cutWidth, cutHeight, 2, cutoutX, cutoutY);
                    var __radius = cutWidth / 2;
                    var __rectHeight = cutHeight - __radius;
                    perimeter2 = 2 * (cutWidth + __rectHeight) + (Math.PI * __radius);
                    break;
                case 8:
                case 9:
                case 10:
                    var cutWidth = getValue('text_38', 20);
                    var cutHeight = getValue('text_39', 40);
                    var triangleArg = (type === 10) ? 1 : (type === 9) ? 0 : undefined;
                    triangle(cutWidth, cutHeight, triangleArg, 2, cutoutX, cutoutY);
                    perimeter2 = cutHeight + cutWidth + Math.sqrt(cutHeight * cutHeight + cutWidth * cutWidth);
                    break;
                case 11:
                    var cutRadiusCircle = getValue('text_45', 30);
                    hexagon(cutRadiusCircle, 2, cutoutX, cutoutY);
                    perimeter2 = cutRadiusCircle * 6;
                    break;
                case 12: // tailWidth, headWidth, tailHeight, headHeight
                    var tw = getValue('text_66', 50);
                    var hw = getValue('text_67', 20);
                    var th = getValue('text_68', 20);
                    var hh = getValue('text_69', 20);
                    arrow(tw, hw, th, hh, 2, cutoutX, cutoutY);
                    let tailP = (tw + th) * 2;
                    let headP = hw + hh + Math.sqrt(hw * hw + hh * hh);

                    perimeter2 = tailP + headP - th;
                    break;
                case 13:
                    var pointsStar = 5;
                    var outerRadius = getValue('text_64', 50);
                    var innerRadius = getValue('text_65', 20);
                    star(outerRadius, innerRadius, 5, 2, cutoutX, cutoutY);
                    let angleRad = Math.PI / pointsStar;
                    let segmentLength = Math.sqrt(Math.pow(outerRadius, 2) + Math.pow(innerRadius, 2) - 2 * outerRadius * innerRadius * Math.cos(angleRad));
                    perimeter2 = segmentLength * 2 * pointsStar;
                    break;
                default:
                    withArrows = false;
                    perimeter2 = 0;
                    break;
            }

            var rotation = getValue('text_42', 0);
            var cutoutContainer = svg.select("#couOutMain");
            if (cutoutContainer) {
                cutoutContainer.transform(`r${rotation},${cutoutX},${cutoutY}`);
            }
            
            if(withArrows){
                arrows = arrowsGroup;
                const outsideOffset = offset + extraSpace;
                const topGuideY = -outsideOffset;
                const leftGuideX = -outsideOffset;
                const connectorAttrs = {
                    stroke: "#000",
                    strokeWidth: 1,
                    strokeDasharray: "1, 1"
                };

                // Draw cutout position dimensions outside the main shape.
                drawDimensionWithText(0, topGuideY, cutoutX, topGuideY, 'X: ', `${demX.toFixed(2)} mm`, '', 2);
                drawDimensionWithText(leftGuideX, 0, leftGuideX, cutoutY, 'Y: ', `${demY.toFixed(2)} mm`, 'vertical', 2);

                // Dotted connectors from old internal arrow points to new outside dimensions.
                arrowsGroup.line(0, cutoutY, 0, topGuideY).attr(connectorAttrs);
                arrowsGroup.line(cutoutX, cutoutY, cutoutX, topGuideY).attr(connectorAttrs);
                arrowsGroup.line(cutoutX, 0, leftGuideX, 0).attr(connectorAttrs);
                arrowsGroup.line(cutoutX, cutoutY, leftGuideX, cutoutY).attr(connectorAttrs);
            }
        }

        function rect(width, height, drawRadius, type = 1, xx = 0, yy = 0) {
            const scaledWidth = scale(width);
            const scaledHeight = scale(height);
            const scaledRadius = scale(drawRadius);

            let adjustedX = xx;
            let adjustedY = yy;

            if (type === 2) {
                adjustedX = xx - scaledWidth / 2;
                adjustedY = yy - scaledHeight / 2;
            }

            shaper.rect(adjustedX, adjustedY, scaledWidth, scaledHeight, drawRadius).attr(mainAttrs(type));

            var offSet = textOffset;

            if (type === 1) {
                updateViewBox(scaledWidth, scaledHeight);
            } else if (type === 2) {
                offSet -= 10;
            }

            drawDimensionWithText(
                adjustedX,
                adjustedY + scaledHeight + offSet,
                adjustedX + scaledWidth,
                adjustedY + scaledHeight + offSet,
                `${idxr_tr_width}: `,
                `${width} mm`,
                '',
                type
            );

            drawDimensionWithText(
                adjustedX - offSet,
                adjustedY,
                adjustedX - offSet,
                adjustedY + scaledHeight,
                `${idxr_tr_height}: `,
                `${height} mm`,
                'vertical',
                type
            );

            
            // If drawRadius is greater than 1, add dashed line and text for radius in top-right corner
            if (drawRadius > 1) {
                // Calculate top-right corner coordinates
                const cornerX1 = adjustedX + scaledWidth - scaledRadius;
                const cornerY1 = adjustedY + scaledRadius;
                const cornerX2 = adjustedX + scaledWidth - scaledRadius;
                const cornerY2 = adjustedY - 10;
        
                // Draw dashed line at 45-degree angle from the corner
                shaper.line(cornerX1, cornerY1, cornerX1 + scaledRadius + 10 , cornerY1).attr({
                    stroke: "red",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2"
                });
                shaper.line(cornerX2, cornerY2, cornerX2 , adjustedY + scaledRadius).attr({
                    stroke: "red",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2"
                });
        
                // Draw text for the radius length
                drawDimensionWithText(
                    cornerX1, cornerY2, cornerX1 + scaledRadius + 10 , cornerY2,
                    `${idxr_tr_radius}: `,
                    `${drawRadius} mm`,
                    'horizontal',
                    type
                );
            }
        }

        function rect_draw(width, height, topLeftRadius, topRightRadius, bottomLeftRadius, bottomRightRadius, type = 1, xx = 0, yy = 0) {
            const scaledWidth = scale(width);
            const scaledHeight = scale(height);
            const scaledTopLeftRadius = scale(topLeftRadius);
            const scaledTopRightRadius = scale(topRightRadius);
            const scaledBottomLeftRadius = scale(bottomLeftRadius);
            const scaledBottomRightRadius = scale(bottomRightRadius);
        
            let adjustedX = xx;
            let adjustedY = yy;

            if (type === 2) {
                adjustedX = xx - scaledWidth / 2;
                adjustedY = yy - scaledHeight / 2;
            }

            // Define the path string for a rectangle with individual corner radii
            var pathString = [
                "M", adjustedX + scaledTopLeftRadius, adjustedY, // Move to the top-left corner with adjusted X and Y
                "H", adjustedX + scaledWidth - scaledTopRightRadius, // Draw top line, leaving room for the top-right corner
                "A", scaledTopRightRadius, scaledTopRightRadius, 0, 0, 1, adjustedX + scaledWidth, adjustedY + scaledTopRightRadius, // Top-right corner
                "V", adjustedY + scaledHeight - scaledBottomRightRadius, // Draw right line, leaving room for the bottom-right corner
                "A", scaledBottomRightRadius, scaledBottomRightRadius, 0, 0, 1, adjustedX + scaledWidth - scaledBottomRightRadius, adjustedY + scaledHeight, // Bottom-right corner
                "H", adjustedX + scaledBottomLeftRadius, // Draw bottom line, leaving room for the bottom-left corner
                "A", scaledBottomLeftRadius, scaledBottomLeftRadius, 0, 0, 1, adjustedX, adjustedY + scaledHeight - scaledBottomLeftRadius, // Bottom-left corner
                "V", adjustedY + scaledTopLeftRadius, // Draw left line, leaving room for the top-left corner
                "A", scaledTopLeftRadius, scaledTopLeftRadius, 0, 0, 1, adjustedX + scaledTopLeftRadius, adjustedY, // Top-left corner
                "Z"
            ].join(" ");

        
            // Create the path element with the above path string
            shaper.path(pathString).attr(mainAttrs(type));
        
            var offSet = textOffset;
        
            if (type === 1) {
                updateViewBox(scaledWidth, scaledHeight);
            } else if (type === 2) {
                offSet -= 10;
            }
        
            drawDimensionWithText(
                adjustedX,
                adjustedY + scaledHeight + offSet,
                adjustedX + scaledWidth,
                adjustedY + scaledHeight + offSet,
                `${idxr_tr_width}: `,
                `${width} mm`,
                '',
                type
            );
        
            drawDimensionWithText(
                adjustedX - offSet,
                adjustedY,
                adjustedX - offSet,
                adjustedY + scaledHeight,
                `${idxr_tr_height}: `,
                `${height} mm`,
                'vertical',
                type
            );
        
            // Add dashed lines and text for each radius if any radius is greater than 1
            if (scaledTopLeftRadius > 1) {
                // Top-left corner dashed lines
                const cornerX1 = adjustedX + scaledTopLeftRadius;
                const cornerY1 = adjustedY + scaledTopLeftRadius;
        
                shaper.line(cornerX1, cornerY1, cornerX1 - scaledTopLeftRadius - 10, cornerY1).attr({
                    stroke: "green",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2"
                });
                shaper.line(cornerX1, adjustedY, cornerX1, cornerY1).attr({
                    stroke: "green",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2"
                });
        
                drawDimensionWithText(
                    cornerX1 - scaledTopLeftRadius - 10, cornerY1,
                    cornerX1, cornerY1,
                    `${idxr_tr_radius}: `,
                    `${topLeftRadius} mm`,
                    'horizontal',
                    2
                );
            }
        
            if (scaledTopRightRadius > 1) {
                // Top-right corner dashed lines
                const cornerX2 = adjustedX + scaledWidth - scaledTopRightRadius;
                const cornerY2 = adjustedY + scaledTopRightRadius;
        
                shaper.line(cornerX2, cornerY2, cornerX2 + scaledTopRightRadius + 10, cornerY2).attr({
                    stroke: "red",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2"
                });
                shaper.line(cornerX2, adjustedY, cornerX2, cornerY2).attr({
                    stroke: "red",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2"
                });
        
                drawDimensionWithText(
                    cornerX2 + scaledTopRightRadius + 10, cornerY2,
                    cornerX2, cornerY2,
                    `${idxr_tr_radius}: `,
                    `${topRightRadius} mm`,
                    'horizontal',
                    2
                );
            }
        
            if (scaledBottomLeftRadius > 1) {
                // Bottom-left corner dashed lines
                const cornerX3 = adjustedX + scaledBottomLeftRadius;
                const cornerY3 = adjustedY + scaledHeight - scaledBottomLeftRadius;
        
                shaper.line(cornerX3, cornerY3, cornerX3 - scaledBottomLeftRadius - 10, cornerY3).attr({
                    stroke: "red",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2"
                });
                shaper.line(cornerX3, adjustedY + scaledHeight, cornerX3, cornerY3).attr({
                    stroke: "red",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2"
                });
        
                drawDimensionWithText(
                    cornerX3 - scaledBottomLeftRadius - 10, cornerY3,
                    cornerX3, cornerY3,
                    `${idxr_tr_radius}: `,
                    `${bottomLeftRadius} mm`,
                    'horizontal',
                    2
                );
            }
        
            if (scaledBottomRightRadius > 1) {
                // Bottom-right corner dashed lines
                const cornerX4 = adjustedX + scaledWidth - scaledBottomRightRadius;
                const cornerY4 = adjustedY + scaledHeight - scaledBottomRightRadius;
        
                shaper.line(cornerX4, cornerY4, cornerX4 + scaledBottomRightRadius + 10, cornerY4).attr({
                    stroke: "red",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2"
                });
                shaper.line(cornerX4, adjustedY + scaledHeight, cornerX4, cornerY4).attr({
                    stroke: "red",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2"
                });
        
                drawDimensionWithText(
                    cornerX4 + scaledBottomRightRadius + 10, cornerY4,
                    cornerX4, cornerY4,
                    `${idxr_tr_radius}: `,
                    `${bottomRightRadius} mm`,
                    'horizontal',
                    2
                );
            }
        }
        
        function circle(mainRadius, type = 1, xx = 0, yy = 0) {
            const radius = mainRadius / 2;
            const scaledRadius = scale(radius);

            let centerX = xx;
            let centerY = yy;

            if (type === 2) {

                centerX = xx;
                centerY = yy;
            } else {

                centerX = xx + scaledRadius;
                centerY = yy + scaledRadius;
            }

            shaper.circle(centerX, centerY, scaledRadius).attr(mainAttrs(type));

            if (type === 1) {
                updateViewBox(scaledRadius * 2, scaledRadius * 2);
            }

            drawDimensionWithText(
                centerX - scaledRadius,
                centerY + scaledRadius + offset,
                centerX + scaledRadius,
                centerY + scaledRadius + offset,
                `${idxr_tr_diameter}: `,
                `${mainRadius} mm`,
                '',
                type
            );
        }

        function halfCircle(mainRadius, type = 1, xx = 0, yy = 0) {
            const radius = mainRadius / 2;
            const scaledRadius = scale(radius);

            let startX = xx;
            let startY = yy;

            if (type === 2) {

                startX = xx - scaledRadius;
                startY = yy - scaledRadius;
            }

            const pathData = `
                M ${startX},${startY + scaledRadius} 
                A ${scaledRadius},${scaledRadius} 0 0,1 ${startX + 2 * scaledRadius},${startY + scaledRadius}
            `;

            shaper.path(pathData).attr(mainAttrs(type));

            if (type === 1) {
                updateViewBox(scaledRadius * 2, scaledRadius);
            }

            drawDimensionWithText(
                startX,
                startY + scaledRadius,
                startX + 2 * scaledRadius,
                startY + scaledRadius,
                `${idxr_tr_diameter}: `,
                `${mainRadius} mm`,
                '',
                type
            );
        }

        function ellipse(width, height, type = 1, x = 0, y = 0) {
            const scaledWidth = scale(width / 2);
            const scaledHeight = scale(height / 2);
            const scaledWidth2 = scaledWidth * 2;
            const scaledHeight2 = scaledHeight * 2;

            let centerX = x;
            let centerY = y;

            if (type === 2) {

                centerX = x;
                centerY = y;
            } else {

                centerX = x + scaledWidth;
                centerY = y + scaledHeight;
            }

            shaper.ellipse(centerX, centerY, scaledWidth, scaledHeight).attr(mainAttrs(type));

            var offSet = textOffset;

            if (type === 1) {
                updateViewBox(scaledWidth2, scaledHeight2);
            } else if (type === 2) {
                offSet -= 10;
            }

            drawDimensionWithText(
                centerX - scaledWidth,
                centerY + scaledHeight + offSet,
                centerX + scaledWidth,
                centerY + scaledHeight + offSet,
                `${idxr_tr_width}: `,
                `${width} mm`,
                '',
                type
            );

            drawDimensionWithText(
                centerX - scaledWidth - offSet,
                centerY - scaledHeight,
                centerX - scaledWidth - offSet,
                centerY + scaledHeight,
                `${idxr_tr_height}: `,
                `${height} mm`,
                'vertical',
                type
            );
        }

        function trapezoidalRight(baseWidth, topWidth, height, type = 1, xx = 0, yy = 0) {
            const scaledBaseWidth = scale(baseWidth);
            const scaledTopWidth = scale(height);
            const scaledHeight = scale(topWidth);

            let startX = xx;
            let startY = yy;

            if (type === 2) {

                startX = xx - Math.max(scaledBaseWidth, scaledTopWidth) / 2;
                startY = yy - scaledHeight / 2;
            }

            const x1 = startX;
            const y1 = startY + scaledHeight;
            const x2 = startX + scaledBaseWidth;
            const y2 = startY + scaledHeight;
            const x3 = startX + scaledTopWidth;
            const y3 = startY;

            const pathData = `
                M ${x1},${y1} 
                L ${x2},${y2} 
                L ${x3},${y3} 
                L ${x1},${y3} 
                Z
            `;

            shaper.path(pathData).attr(mainAttrs(type));

            var offSet = textOffset;

            if (type === 1) {
                const totalWidth = Math.max(scaledBaseWidth, scaledTopWidth);
                updateViewBox(totalWidth, scaledHeight);
            } else if (type === 2) {
                offSet -= 10;
            }

            drawDimensionWithText(x1, y1 + offSet, x2, y1 + offSet, `${idxr_tr_width}: `, `${baseWidth} mm`, '', type);
            drawDimensionWithText(x1, y3 - offSet, x3, y3 - offSet, 'Longueur: ', `${height} mm`, 'horizontal', type);
            drawDimensionWithText(x1 - offSet, y3, x1 - offSet, y1, `${idxr_tr_height}: `, `${topWidth} mm`, 'vertical', type);
        }

        function trapezoidalIsosceles(baseWidth, topWidth, height, type = 1, xx = 0, yy = 0) {
            const scaledBaseWidth = scale(baseWidth);
            const scaledTopWidth = scale(topWidth);
            const scaledHeight = scale(height);
        
            const offsetX = (scaledBaseWidth - scaledTopWidth) / 2;
            const regulator = scaledBaseWidth - scaledTopWidth > 0 ? 0 : Math.abs(offsetX) ;
            let startX = xx + regulator;
            let startY = yy;
        
            if (type === 2) {
                // Adjust startX based on offsetX to center the isosceles trapezoid
                startX = xx - scaledBaseWidth / 2 + offsetX;
                startY = yy - scaledHeight / 2;
            }
        
            const x1 = startX;
            const y1 = startY + scaledHeight;
            const x2 = startX + scaledBaseWidth;
            const y2 = startY + scaledHeight;
            const x3 = startX + offsetX + scaledTopWidth;
            const y3 = startY;
            const x4 = startX + offsetX;
        
            const pathData = `
                M ${x1},${y1} 
                L ${x2},${y2} 
                L ${x3},${y3} 
                L ${x4},${y3} 
                Z
            `;
        
            shaper.path(pathData).attr(mainAttrs(type));
        
            var offSet = textOffset;
        
            if (type === 1) {
                const totalWidth = Math.max(scaledBaseWidth, scaledTopWidth);
                updateViewBox(totalWidth, scaledHeight);
            } else if (type === 2) {
                offSet -= 10;
            }
        
            drawDimensionWithText(x1, y1 + offSet, x2, y1 + offSet, `${idxr_tr_width}: `, `${baseWidth} mm`, '', type);
            drawDimensionWithText(x4, y3 - offSet, x3, y3 - offSet, 'Longueur: ', `${topWidth} mm`, 'horizontal', type);
            drawDimensionWithText(x1 - startX - offSet, y3, x1 - startX - offSet, y1, `${idxr_tr_height}: `, `${height} mm`, 'vertical', type);
        }

        function door(width, height, type = 1, xx = 0, yy = 0) {
            const scaledWidth = scale(width);
            const scaledHeight = scale(height);
            const rad = scaledWidth / 2;
        
            let startX = xx;
            let startY = yy;
        
            if (type === 2) {
                startX = xx - scaledWidth / 2;
                startY = yy - scaledHeight / 2;
            }
        
            // Define the main door path including the arc
            var doorPath = `
                M${startX},${startY + scaledHeight} 
                v${-scaledHeight + rad} 
                a${rad},${rad} 0 0,1 ${scaledWidth},0 
                v${scaledHeight - rad} 
                l ${-scaledWidth},0
            `;
        
            // Draw the main door path
            shaper.path(doorPath).attr(mainAttrs(type));
        
            // Add a dashed line to represent the diameter of the half-circle at the top
            const diameterLine = `
                M${startX},${startY + scaledHeight - scaledHeight + rad} 
                H${startX + scaledWidth}
            `;
        
            // Draw the dashed line across the diameter of the half-circle
            shaper.path(diameterLine).attr({
                stroke: '#065075',
                fill: 'none',
                'stroke-width': 1,
                'stroke-dasharray': '4, 4' // Dashed line pattern
            });
        
            var offSet = textOffset;
        
            if (type === 1) {
                updateViewBox(scaledWidth, scaledHeight);
            } else if (type === 2) {
                offSet -= 10;
            }
        
            // Draw width and height dimensions
            drawDimensionWithText(
                startX,
                startY + scaledHeight + offSet,
                startX + scaledWidth,
                startY + scaledHeight + offSet,
                `${idxr_tr_width}: `,
                `${width} mm`,
                '',
                type
            );
            drawDimensionWithText(
                startX - offSet,
                startY,
                startX - offSet,
                startY + scaledHeight,
                'Longueur: ',
                `${height} mm`,
                'vertical',
                type
            );
        
            // Draw a dimension line from the center of the top dashed line to the center of the bottom line
            const centerX = startX + scaledWidth / 2;
            const topCenterY = startY + rad;
            const bottomCenterY = startY + scaledHeight;
        
            drawDimensionWithText(
                centerX,
                topCenterY,
                centerX,
                bottomCenterY,
                `${idxr_tr_height}: `,
                `${height-(width/2)} mm`,
                'vertical',
                type
            );
        }

        function triangle(width, height, mainType = 2, type = 1, xx = 0, yy = 0) {
            const scaledWidth = scale(width);
            const scaledHeight = scale(height);

            let topPointX = 0;
            if (mainType === 1) topPointX = scaledWidth;
            else if (mainType === 2) topPointX = scaledWidth / 2;

            let startX = xx;
            let startY = yy;

            if (type === 2) {

                startX = xx - scaledWidth / 2;
                startY = yy - scaledHeight / 2;
            }

            var path = `
                M${startX},${startY + scaledHeight} 
                L${startX + topPointX},${startY} 
                L${startX + scaledWidth},${startY + scaledHeight} 
                Z
            `;

            shaper.path(path).attr(mainAttrs(type));

            var offSet = textOffset;

            if (type === 1) {
                updateViewBox(scaledWidth, scaledHeight);
            } else if (type === 2) {
                offSet -= 10;
            }

            drawDimensionWithText(
                startX,
                startY + scaledHeight + offSet,
                startX + scaledWidth,
                startY + scaledHeight + offSet,
                `${idxr_tr_base}: `,
                `${width} mm`,
                '',
                type
            );
            drawDimensionWithText(
                startX - offSet,
                startY,
                startX - offSet,
                startY + scaledHeight,
                'Longueur: ',
                `${height} mm`,
                'vertical',
                type
            );
        }

        function hexagon(sideLength, type = 1, xx = 0, yy = 0) {
            const scaledSideLength = scale(sideLength);
            const scaledHeight = scaledSideLength * Math.sqrt(3);

            let startX = xx;
            let startY = yy;

            if (type === 2) {

                startX = xx - scaledSideLength;
                startY = yy - scaledHeight / 2;
            }

            const points = [
                [startX + scaledSideLength / 2, startY],
                [startX + scaledSideLength * 1.5, startY],
                [startX + scaledSideLength * 2, startY + scaledHeight / 2],
                [startX + scaledSideLength * 1.5, startY + scaledHeight],
                [startX + scaledSideLength / 2, startY + scaledHeight],
                [startX, startY + scaledHeight / 2]
            ].map(point => point.join(',')).join(' ');

            shaper.polygon(points).attr(mainAttrs(type));

            var offSet = textOffset;

            if (type === 1) {
                updateViewBox(2 * scaledSideLength, scaledHeight);
            } else if (type === 2) {
                offSet -= 10;
            }

            drawDimensionWithText(
                startX + scaledSideLength / 2,
                startY + scaledHeight + offSet,
                startX + scaledSideLength * 1.5,
                startY + scaledHeight + offSet,
                `${idxr_tr_side}: `,
                `${sideLength} mm`,
                '',
                type
            );
            drawDimensionWithText(
                startX ,
                startY - offSet,
                startX + scaledSideLength * 2,
                startY - offSet,
                `${idxr_tr_width}: `,
                `${sideLength*2} mm`,
                '',
                type
            );
            drawDimensionWithText(
                startX - offSet,
                startY,
                startX - offSet,
                startY + scaledHeight,
                `${idxr_tr_height}: `,
                `${(sideLength*Math.sqrt(3)).toFixed(2)} mm`,
                'vertical',
                type
            );
        }

        function arrow(tailWidth, headWidth, tailHeight, headHeight, type = 1, xx = 0, yy = 0) {

            var tw = scale(tailWidth);
            var hw = scale(headWidth);
            var th = scale(tailHeight);
            var hh = scale(headHeight);

            const tailEndX = xx + tw;
            const headEndX = tailEndX + hw;
            arrowsGroup.circle(xx, yy, 2)
            const arrowPath = `
                M ${xx},${yy + th}         
                L ${xx},${yy}                 
                L ${tailEndX},${yy}           
                L ${tailEndX},${yy - hh}      
                L ${tailEndX + hw},${yy + th / 2}  
                L ${tailEndX},${yy + th + hh} 
                L ${tailEndX},${yy + th}      
                Z                             
            `;

            if (type === 1) {
                svg.attr({
                    viewBox: `${xx - 50} ${yy - hh - 50} ${headEndX - xx + 100} ${th + hh + 100}`
                });
            }

            shaper.path(arrowPath).attr(mainAttrs(type));

            var offSet = textOffset;
            if (type === 2) {
                offSet -= 10;
            }

            drawDimensionWithText(
                xx,
                yy - 10,
                tailEndX,
                yy - 10,
                'L.Q: ',
                `${tailWidth} mm`,
                '',
                type
            );

            drawDimensionWithText(
                tailEndX,
                yy - hh - 10,
                headEndX,
                yy - hh - 10,
                'L.T: ',
                `${headWidth} mm`,
                '',
                type
            );

            drawDimensionWithText(
                xx - offSet,
                yy,
                xx - offSet,
                yy + th,
                'H.Q: ',
                `${tailHeight} mm`,
                'vertical',
                type
            );

            drawDimensionWithText(
                tailEndX - offSet,
                yy + th,
                tailEndX - offSet,
                yy + th + hh,
                'H.T: ',
                `${headHeight} mm`,
                'vertical',
                type
            );
        }

        function star(outerRadius, innerRadius, numPoints, type = 1, xx = 0, yy = 0) {
            const scaledOuterRadius = scale(outerRadius);
            const scaledInnerRadius = scale(innerRadius);

            let startX = xx;
            let startY = yy;

            if (type === 2) {

                startX = xx - scaledOuterRadius;
                startY = yy - scaledOuterRadius;
            }

            const points = [];
            const angleStep = Math.PI / numPoints;
            let firstOuterPoint, firstInnerPoint;

            for (let i = 0; i < 2 * numPoints; i++) {
                const angle = i * angleStep - Math.PI / 2;
                const r = (i % 2 === 0) ? scaledOuterRadius : scaledInnerRadius;
                const x = r * Math.cos(angle);
                const y = r * Math.sin(angle);
                points.push([x + startX + scaledOuterRadius, y + startY + scaledOuterRadius]);

                if (i === 0) firstOuterPoint = {
                    x: x + startX + scaledOuterRadius,
                    y: y + startY + scaledOuterRadius
                };
                if (i === 1) firstInnerPoint = {
                    x: x + startX + scaledOuterRadius,
                    y: y + startY + scaledOuterRadius
                };
            }

            const pointString = points.map(point => point.join(',')).join(' ');

            shaper.polygon(pointString).attr(mainAttrs(type));

            var offSet = textOffset;

            if (type === 1) {
                const maxRadius = Math.max(scaledOuterRadius, scaledInnerRadius);
                updateViewBox(2 * maxRadius + startX, 2 * maxRadius + startY);
            } else if (type === 2) {
                offSet -= 10;
            }

            const centerX = startX + scaledOuterRadius;
            const centerY = startY + scaledOuterRadius;

            drawDimensionWithText(
                centerX, centerY,
                firstOuterPoint.x,
                firstOuterPoint.y,
                `${idxr_tr_outer_radius}: `,
                `${outerRadius} mm`,
                'horizontal',
                type
            );

            drawDimensionWithText(
                centerX, centerY,
                firstInnerPoint.x, firstInnerPoint.y,
                `${idxr_tr_inner_radius}: `,
                `${innerRadius} mm`,
                'horizontal',
                type
            );
        }
        
        function egg(width, height, type = 1, xx = 0, yy = 0) {
            // Scaling factors based on the original SVG dimensions
            const originalWidth = 4625 * 2; // Approximate original width based on SVG path
            const originalHeight = 12788; // Approximate original height based on SVG path
            const scaleX = width / originalWidth;
            const scaleY = height / originalHeight;
        
            let centerX = xx;
            let centerY = yy;
        
            if (type === 2) {
                // Center the shape around (xx, yy)
                centerX = xx;
                centerY = yy;
            } else {
                // Position the egg based on top-left corner
                centerX = xx + (width / 2);
                centerY = yy + (height / 2);
            }
        
            // Path data for the egg shape from SVG
            const pathData = `
                M4625 12788 c-247 -47 -315 -62 -420 -92 -238 -70 -524 -196 -755 -333 -1361 -812 
                -2398 -2492 -3038 -4923 -189 -717 -316 -1380 -366 -1915 -54 -565 -53 -880 0 -1380 
                169 -1582 985 -2781 2380 -3497 609 -312 1313 -529 1994 -613 69 -8 154 -20 189 -26 
                48 -9 84 -9 145 0 45 6 141 18 214 26 682 78 1422 323 2007 666 1014 592 1702 1496 
                2036 2674 128 450 188 828 229 1445 22 326 25 513 10 688 -5 65 -14 200 -20 302 
                -25 431 -93 973 -155 1250 -100 442 -265 1016 -420 1464 -631 1826 -1476 3124 
                -2453 3770 -394 260 -797 416 -1217 471 -66 8 -148 20 -181 25 -71 11 -109 11 -179 -2z
            `;
        
            // Draw the egg path, scaled for the desired width and height
            const scaledPath = shaper.path(pathData).transform(`scale(${scaleX}, ${scaleY}) translate(${centerX}, ${centerY})`);
            scaledPath.attr(mainAttrs(type));
        
            // Set dimensions for width and height
            const offSet = textOffset;
        
            if (type === 1) {
                updateViewBox(width, height);
            } else if (type === 2) {
                offSet -= 10;
            }
        
            // Draw width and height dimensions
            drawDimensionWithText(
                centerX - (width / 2),
                centerY + (height / 2) + offSet,
                centerX + (width / 2),
                centerY + (height / 2) + offSet,
                `${idxr_tr_width}: `,
                `${width} mm`,
                '',
                type
            );
        
            drawDimensionWithText(
                centerX - (width / 2) - offSet,
                centerY - (height / 2),
                centerX - (width / 2) - offSet,
                centerY + (height / 2),
                `${idxr_tr_height}: `,
                `${height} mm`,
                'vertical',
                type
            );
        }
        
        async function text(text, fontFamily, width) {
            showLoader();
            const scaledWidth = scale(width);
            const fontSize = (scaledWidth / text.length) * 2;
        
            // Load a font (use a local or Google Fonts URL if not using a default font)
            const fontUrl = fontFamily; 
            const font = await opentype.load(fontUrl);
        
            // Generate path data for the text
            const pathData = font.getPath(text, 0, fontSize, fontSize).toPathData();
        
            // Create an SVG path with the generated path data
            try {
                shapeGroup.path(pathData).attr({
                    fill: '#ADEFFF',
                    stroke: '#065075',
                    "stroke-width": 1,
                });
            } catch(error) {
                console.log(error);
                
                shapeGroup.text(scaledWidth / 2, fontSize, text).attr({
                    "font-family": fontFamily,
                    "font-size": fontSize,
                    fill: '#ADEFFF',
                    stroke: '#065075',
                    "stroke-width": 1,
                    "text-anchor": "middle",
                });
            }
        
            let timeout2 = 0;
            if (shapeSettings.start === 1) {
                timeout2 = 600;
                shapeSettings.start = 0;
            }
        
            setTimeout(() => {
                const bbox = shapeGroup.getBBox();
                let textWidth = bbox.width;
                let textHeight = bbox.height;
                var height = width * textHeight / textWidth;
        
                setDemensions(width, height, 0, getCardLabel('card_31_0'));
                perimeter = (width + height) * 2;
        
                const isHeight = $("#demension_height").is(":checked");
                var showWidth = width;
                var showHeight = height;
                if (isHeight) {
                    showHeight = width;
                    showWidth = width * width / height;
                }
        
                // Updated width dimension arrow
                drawDimensionWithText(bbox.x, bbox.y - 30, bbox.x + textWidth, bbox.y - 30, 'L: ', `${showWidth.toFixed(2)} mm`);
        
                // Height dimension arrow remains the same
                drawDimensionWithText(bbox.x + textWidth + 30, bbox.y, bbox.x  + textWidth + 30, bbox.y + textHeight, 'H: ', `${showHeight.toFixed(2)} mm`, 'vertical');
        
                // Adjust the view box to fit the entire text with some margin
                updateViewBox(textWidth + 30, textHeight);
            }, timeout2);
            
            hideLoader();
            copySvgOnly();
        }
        
        // Function to show the loader
        function showLoader() {
            // Check if loader already exists; if not, create it
            if ($("#svgLoader").length === 0) {
                // Create the loader element
                const loader = $('<div>', { id: 'svgLoader' }).css({
                    position: 'absolute',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    width: '40px',
                    height: '40px',
                    border: '4px solid rgba(0, 0, 0, 0.1)',
                    borderTop: '4px solid #333',
                    borderRadius: '50%',
                    animation: 'spin 1s linear infinite',
                    zIndex: 1000 // Ensure it appears above the SVG
                });
                
                // Append the loader to the container
                $('#svgContainer').css('position', 'relative').append(loader);
                
                // Add CSS for spinner animation
                $('<style>')
                    .prop('type', 'text/css')
                    .html(`
                        @keyframes spin {
                            0% { transform: rotate(0deg); }
                            100% { transform: rotate(360deg); }
                        }
                    `)
                    .appendTo('head');
            }
            // Show the loader
            $("#svgLoader").show();
        }

        // Function to hide and remove the loader
        function hideLoader() {
            $("#svgLoader").remove(); // Remove loader if it exists
        }

        function drawDimensionWithText(x1, y1, x2, y2, text = '', boldText = '', orientation = 'horizontal', type = 1) {
            drawArrow(x1, y1, x2, y2, type);
            const fontt = 8;
            const midX = (x1 + x2) / 2;
            const midY = (y1 + y2) / 2;
            if (text || boldText) {
                addText(text, boldText, midX, midY, () => ({
                    'font-size': fontt,
                    'fill': '#065075'
                }), orientation, midX, midY, type);
            }
        }

        function drawArrow(x1, y1, x2, y2) {
            const arrowLength = 5;
            const arrowColor = '#F15735';
            const strokeWidth = 1;
            const attrs = {
                stroke: arrowColor,
                'stroke-width': strokeWidth
            };

            const angle = Math.atan2(y2 - y1, x2 - x1) + Math.PI;

            arrows.line(x1, y1, x2, y2).attr(attrs);

            function drawArrowhead(x, y, angle) {
                const line1X = x - arrowLength * Math.cos(angle + Math.PI / 4);
                const line1Y = y - arrowLength * Math.sin(angle + Math.PI / 4);

                const line2X = x - arrowLength * Math.cos(angle - Math.PI / 4);
                const line2Y = y - arrowLength * Math.sin(angle - Math.PI / 4);
                arrows.line(x, y, line1X, line1Y).attr(attrs);
                arrows.line(x, y, line2X, line2Y).attr(attrs);
            }

            drawArrowhead(x1, y1, angle);
            drawArrowhead(x2, y2, angle + Math.PI);
        }

        function holeDems(cx, cy, diameter, extra, isRadial, space = 0, centerX = 0, centerY = 0) {
            let attrs = {
                stroke: "#000", 
                strokeWidth: 1,
                strokeDasharray: "1, 1"
            };
            let p1 = {
                x: parseFloat(cx) + parseFloat(diameter),
                y: cy,
            };
            let p2 = {
                x: parseFloat(cx) - parseFloat(diameter),
                y: cy,
            };
            
            arrowsGroup.line(p1.x, p1.y, p1.x, - extra - offset/2).attr(attrs);
            arrowsGroup.line(p2.x, p2.y, p2.x, - extra - offset/2).attr(attrs);
            drawDimensionWithText(p1.x, - extra - offset/2, p2.x,  - extra - offset/2, 'Diam : ', `${unScale(diameter)} mm`, 'horizontal', 3);
            if(isRadial !== 0){
                arrowsGroup.circle(centerX, centerY - extra, 2);
                arrowsGroup.line(centerX, - extra, centerX, centerY - extra).attr(attrs);
                drawDimensionWithText(- offset/3, centerY - extra, - offset/3, - extra, 'Y: ', `${unScale(centerY)} mm`, 'vertical', 3);
                
                arrowsGroup.line(centerX, centerY- extra, 0, centerY- extra ).attr(attrs);
                drawDimensionWithText(centerX, - offset/3 - extra, 0, - offset/3 - extra, 'X: ', `${unScale(centerX)} mm`, 'horizontal', 3);
                
                arrowsGroup.line(centerX, centerY- extra, centerX, centerY- extra + isRadial ).attr(attrs);
                arrowsGroup.line(centerX, centerY + isRadial - extra, 0, centerY + isRadial - extra).attr(attrs);
                drawDimensionWithText(- offset/3, centerY - extra + isRadial, - offset/3, centerY + - extra , 'D: ', `${unScale(centerY)} mm`, 'vertical', 3);
            }else{
                arrowsGroup.line(cx, cy, 0, cy).attr(attrs);
                drawDimensionWithText(- offset/3, cy, - offset/3, - extra, 'Dist: ', `${unScale(space)} mm`, 'vertical', 3);
                arrowsGroup.circle(cx, cy, 1);
            }
        }

        function addText(text, boldText, x, y, textAttrFunc, orientation = 'horizontal', originX = 0, originY = 0, type) {
            const padding = 2;
            const fill = type === 1 ? 'rgba(97, 241, 150, 0.73)' : (type === 3 ? 'rgba(255, 165, 0, 0.73)' : 'rgba(83, 246, 181, 0.73)');
            const paddings = 9;
            const moveUpBy = 0;
        
            const textBlock = arrows.text(x, y - paddings, [text, boldText]).attr(textAttrFunc());
            textBlock.select('tspan:nth-child(2)').attr({
                'font-weight': 'bold'
            });
        
            setTimeout(() => {
                const bbox = textBlock.getBBox();
                const bboxWidth = bbox.width;
                const bboxHeight = bbox.height;
                const textCenterX = x - bboxWidth / 2;
                const textCenterY = y - bboxHeight / 2;
        
                textBlock.attr({
                    x: textCenterX,
                    y: textCenterY + moveUpBy
                });

                const centeredBBox = textBlock.getBBox();
                const rectWidth = centeredBBox.width + 2 * padding;
                const rectHeight = ((centeredBBox.height + 3 * padding) * 2) / 3;

                const rect = arrows.rect(
                    centeredBBox.cx - rectWidth / 2,
                    centeredBBox.cy - rectHeight / 2,
                    rectWidth,
                    rectHeight,
                    5
                ).attr({
                    fill
                });
        
                textBlock.before(rect);
         
                // Handle rotations based on orientation
                const transformAngle = orientation === 'vertical' ? -90 : (orientation === 'depth' ? -45 : 0);
                if (transformAngle !== 0) {
                    const transformString = `rotate(${transformAngle} ${originX} ${originY})`;
                    textBlock.transform(transformString);
                    rect.transform(transformString);
                }
                copySvgOnly();
            }, 0);
        }

        function getValue(id, def = 0) {
            const element = $(`#${id}`);
            if(element.length > 0){
                const value = $(`#${id}`).val();
                const parsedValue = parseFloat(value);
                if (['text_25', 'text_26', 'text_40', 'text_41'].includes(id)) {
                    return isNaN(parsedValue) ? def : parsedValue;
                }
                return isNaN(parsedValue) ? def : Math.abs(parsedValue);
            }
            return def;
        }

        function updateViewBox(w, h, e = null) {
            const xx = e ? shapeSettings.x - w / 2 : shapeSettings.x;
            const yy = e ? shapeSettings.y - h / 2 : shapeSettings.y;
            const adjustment = offset + extraSpace / 2;

            svg.attr({
                viewBox: `${xx - adjustment} ${yy - adjustment} ${w + 2 * adjustment} ${h + 2 * adjustment}`
            });
        }

    }

    function arrowsHeads(num = 4) {
        const {
            arrowStart,
            arrowEnd
        } = createArrowMarkers(num);
        if (num == 3) {
            return {
                stroke: "#37d802",
                strokeWidth: 1,
                "markerStart": arrowStart,
                "markerEnd": arrowEnd,
                strokeDasharray: "5, 5"
            };
        } else if (num == 2) {
            return {
                stroke: "#59D2FE",
                strokeWidth: 1,
                "markerStart": arrowStart,
                "markerEnd": arrowEnd,
                strokeDasharray: "1, 2"
            };
        }
        return {
            stroke: "#D0346C",
            strokeWidth: 1,
            "markerStart": arrowStart,
            "markerEnd": arrowEnd
        };
    }

    function horText() {
        var font = "8px";
        if (typeof DefineCubeShapeToDraw !== 'undefined') font = "11px";
        return {
            "font-size": font,
            "text-anchor": "middle",
            fill: "#043781"
        };
    }

    function createArrowMarkers(num = 4) {
        const arrowSize = num;
        const markerOffset = 3;
        const arrowEnd = svg.path(`M0,0 L${arrowSize},${arrowSize/2} L0,${arrowSize} L${arrowSize/3},${arrowSize/2} Z`)
            .attr({
                fill: "#f00"
            }).marker(0, 0, arrowSize, arrowSize, markerOffset, arrowSize / 2);

        const arrowStart = svg.path(`M${arrowSize},0 L0,${arrowSize/2} L${arrowSize},${arrowSize} L${arrowSize-(arrowSize/3)},${arrowSize/2} Z`)
            .attr({
                fill: "#f00"
            }).marker(0, 0, arrowSize, arrowSize, arrowSize - markerOffset, arrowSize / 2);

        return {
            arrowStart,
            arrowEnd
        };
    } 

    function addText(text, boldText, x, y, textAttrFunc, orientation = 'horizontal', originX = 0, originY = 0) {

        const textBlock = shapeGroup.text(x, y, [text, boldText]).attr(textAttrFunc());

        const tspan1 = textBlock.select('tspan:nth-child(1)');
        const tspan2 = textBlock.select('tspan:nth-child(2)').attr({
            'font-weight': 'bold'
        });

        const bbox = textBlock.getBBox();

        const rect = shapeGroup.rect(bbox.x, bbox.y, bbox.width, bbox.height + 2).attr({
            fill: 'white'
        });

        textBlock.before(rect);

        if (orientation === 'vertical') {
            textBlock.transform(`rotate(-90 ${originX} ${originY})`);
            rect.transform(`rotate(-90 ${originX} ${originY})`);
        } else if (orientation === 'depth') {
            textBlock.transform(`rotate(-45 ${originX} ${originY})`);
            rect.transform(`rotate(-45 ${originX} ${originY})`);
        }
    }

    function calculateWeight() {

        if (!cube.on) {
            // let diameter_de_decoupe = (parseFloat(perimeter) + parseFloat(perimeter2));
            $('#diameter_de_decoupe_price').val(parseFloat(perimeter).toFixed(2));
            $('#diameter_de_decoupe_price2').val(parseFloat(perimeter2).toFixed(2));
            $('#p_d_d_map_1').text(`${perimeter.toFixed(2)} + ${perimeter2.toFixed(2)} mm`)
        }

        var thickness_mm = 0;
        var density = 0;

        var productThicknessElement = document.getElementById("product_thickness");
        if (productThicknessElement) {
            thickness_mm = parseFloat(productThicknessElement.value);
        }
        var productDensityElement = document.getElementById("product_density");
        if (productDensityElement) {
            density = parseFloat(productDensityElement.value) * 1000;
        }

        var width_m = width_cm / 1000;
        var height_m = height_cm / 1000;
        var thickness_m = thickness_mm / 1000;

        var surface_m = 0;
        var volume_m = 0;
        var diameterCapot = 0;
        var diameterBase = 0;

        if (cube.on) {
            if (cube.faces === 4) {
                surface_m = 2 * (cube.vitrineDepth * cube.vitrineHeight) + cube.vitrineWidth * cube.vitrineDepth + cube.vitrineHeight * cube.vitrineWidth;
                diameterCapot = cube.vitrineDepth + 2*cube.vitrineHeight + 2*cube.vitrineWidth;
            } else {
                surface_m = 2 * (cube.vitrineWidth * cube.vitrineHeight) + 2 * (cube.vitrineHeight * cube.vitrineDepth) + (cube.vitrineWidth * cube.vitrineDepth);
                diameterCapot = 2*cube.vitrineDepth + 4*cube.vitrineHeight + 2*cube.vitrineWidth;
            }
            var surface_second_m = 0;
            const heigh = cube.socleHeight / 1000;
            if(cube.base){
                // this calculation is based on the base with 5 faces !!
                surface_second_m = 2 * (cube.vitrineWidth * heigh) + 2 * ( heigh * cube.vitrineDepth) + (cube.vitrineWidth * cube.vitrineDepth);
                diameterBase = 2*cube.vitrineWidth + 2*cube.vitrineDepth + 4*heigh
            }
            $('#cube_second_surface').val(surface_second_m);
            volume_m = cube.vitrineWidth * (cube.vitrineHeight + heigh) * cube.vitrineDepth;
        } else {
            surface_m = width_m * height_m;
            volume_m = surface_m * thickness_m;
        }
        
        var weight_kg = volume_m * density;

        // Setting values using jQuery
        $("#product_weight").val(weight_kg.toFixed(4));
        $("#product_width").val(width_cm);
        $("#product_height").val(height_cm);
        $("#product_depth").val(thickness_mm);
        $("#product_volume").val(volume_m.toFixed(5));
        $("#product_surface").val(surface_m.toFixed(3));
        $("#cube_diameter_socle").val(diameterBase.toFixed(3));
        $("#cube_diameter_capot").val(diameterCapot.toFixed(3));
        
        $("#resume_tr_poids .option_title").text(weight_kg ? weight_kg.toFixed(4) + " kg" : "0 kg");
        $("#resume_tr_volume .option_title").text(volume_m ? volume_m.toFixed(5) + " m³" : "0 m³");
        $("#resume_tr_surface .option_title").text(surface_m ? surface_m.toFixed(3) + " m²" : "0 m²");

        // document.querySelector("#resume_tr_epaisseur .option_title").textContent = thickness_mm ? thickness_mm + " mm" : "0 mm";

        return weight_kg;
    }

    return {
        init: init,
        initCube: initCube,
    };

})();

document.addEventListener("DOMContentLoaded", function() {
    
    if ((typeof(Snap) !== 'undefined') && (typeof CustomizationModule !== 'undefined')) {
        if (typeof(DefineCubeShapeToDraw) !== 'undefined') {
            CustomizationModule.initCube();
        } else {
            CustomizationModule.init();
        }
        $("#resume_tr_epaisseur").remove();
    }
    $('input[name="group[6]"]').on('change', function () {
        if (typeof(DefineCubeShapeToDraw) !== 'undefined'){
            setTimeout(function () {
                if (typeof Snap !== 'undefined' && typeof(CustomizationModule) !== 'undefined') {
                    CustomizationModule.initCube();

                    $("#resume_tr_epaisseur").remove();
                    $("#preloader-overlay-xyz123").remove();
                }
            }, 1000); 
        }
    });
});
