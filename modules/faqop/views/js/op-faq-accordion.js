/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */

//addEventListener on mouse click
document.addEventListener('click', function (e) {
    let toggleClass = '.op-accordion-toggle';
    let toggleElement = false;

    if (e.target.matches(toggleClass)) {
        toggleElement = e.target;
    } else {
        toggleElement = getParents(e.target, toggleClass);
    }

    if (toggleElement) {
        //check if element contains active class
        if (!toggleElement.parentElement.classList.contains('op-accordion-active')) {

            //remove active class from all other accordions if class op-accordion-collapsable exists
            var accordions = document.querySelectorAll('.op-accordion-active.op-accordion-collapsable');
            for (var i = 0; i < accordions.length; i++) {
                document.querySelector('.op-accordion-active .op-accordion-panel').style.height = "0px";
                accordions[i].classList.remove('op-accordion-active');
            }

            //add active class on cliked accordion
            toggleElement.parentElement.classList.add('op-accordion-active');

            var container = toggleElement.parentElement.querySelector('.op-accordion-panel');
            container.style.height = "auto"

            /** Get the computed height of the container. */
            var height = container.clientHeight + "px"

            /** Set the height of the content as 0px, */
            /** so we can trigger the slide down animation. */
            container.style.height = "0px"

            /** Do this after the 0px has applied. */
            /** It's like a delay or something. MAGIC! */
            setTimeout(() => {
                container.style.height = height
            }, 0)
        } else {
            //remove active class on cliked accordion
            toggleElement.parentElement.querySelector('.op-accordion-panel').style.height = "0px"
            toggleElement.parentElement.classList.remove('op-accordion-active');
        }
    }
});

var getParents = function (elem, parent_name) {
    for ( ; elem && elem !== document; elem = elem.parentNode ) {
        if (elem.matches(parent_name)) {
            return elem;
        }
    }
    return false;
};
