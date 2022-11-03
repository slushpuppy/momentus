import CSS from './css/style.module.scss';
import Env from 'bootloader';


let $ = require('jquery');

jQuery.fn.extend({

   bottomSheet: function ({closeBtn= false, scrollFix = true, canResize=true, height= {min:"25vh",max: "50vh"},onClose = () =>{}}) {
      let comp  = $(this);
      comp.attr('data-ui-component-bottomSheet','');

      comp.css('max-height',height.max);

      let sheetHandleContents = ``;

      if (canResize) sheetHandleContents += `<i class="fas fa-ellipsis-h ${CSS.sheetHandle}"></i>`;

      if (closeBtn) sheetHandleContents += `<i class="fas fa-times ${CSS.closeBtn}"></i>`;

      comp.prepend(`<div class="${CSS.sheetHeader}">${sheetHandleContents}</div>`);
      let sheetHeader = comp.find(`.${CSS.sheetHeader} `);

      let scrollFixElement;
      if (scrollFix)
      {
         $(this).parent().append(`<div class="${CSS.scrollFix}"></div>`);
         scrollFixElement = $(`.${CSS.scrollFix}`);
      }

      let hideBottomSheet = () => {
         comp.css('height',0);
         if (scrollFix)
         {
            scrollFixElement.css('height',0);
         }
      }

      return {
         show: () => {
            comp.css('height',height.min);

            if (scrollFix)
            {
               scrollFixElement.css('height', height.min);
            }

            $(`.${CSS.closeBtn}`).click((evt) => {
               onClose();
               hideBottomSheet();
            });



            let toggleSheet = function () {
               let heightToSet;
               if (comp.css(height) < height.max)
               {
                  heightToSet = height.max;
               }
               else
               {
                  heightToSet = height.min;
               }
               comp.css(height,heightToSet);
               if (scrollFix)
               {
                  scrollFixElement.css('height', heightToSet);
               }
            };
           if (Env.isMobileBrowser())
            {



            } else {
               comp.click((evt) => {
                  if (evt.target !== comp[0] && evt.target !== sheetHeader[0])
                     return;
                  toggleSheet();
               })
            }
         },
         hide : () => {
            hideBottomSheet();
         },
         remove: () => {

            comp.html('').removeAttr('data-ui-component-bottomSheet');

            $(`.${CSS.scrollFix}`).remove();
            onClose();
         }
      };


   }
});