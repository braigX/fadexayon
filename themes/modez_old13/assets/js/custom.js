/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */
/*************************************************************** */
document.addEventListener("DOMContentLoaded", function () {
  let header = document.getElementById("header");
  let sticky = header.offsetTop;

  window.addEventListener("scroll", function () {
      if (window.scrollY > sticky) {
          header.classList.add("sticky");
      } else {
          header.classList.remove("sticky");
      }
  });
});



/*Function modif section blog page home */
$(document).ready(function () {
  // Vérifier si la largeur de l'écran est supérieure à 991 pixels
  if (window.innerWidth > 991) {
    var $firstArticle = $(".page_home .ybc_blog_content_block_item:first-child");
    // Déplacer le premier article dans la balise <div class="ybc-first-column">
    $firstArticle.wrap('<div class="ybc-first-column"></div>');
    // Cibler les autres articles (tous sauf le premier)
    var $otherArticles = $(
      ".page_home .ybc_blog_content_block_item:not(:first-child)"
    );
    // Déplacer les autres articles dans la balise <div class="ybc-last-column">
    $otherArticles.wrapAll('<div class="ybc-last-column"></div>');
    
    $('.js-cart-line-product-quantity').prop('disabled', false);
  }
});
/**************************************************** */
// firstWord
document.addEventListener("DOMContentLoaded", function () {
  if (document.body.classList.contains("page-product")) {
    const titleElementF = document.querySelector(
      ".first .products-section-title"
    );
    if (titleElementF) {
      const titleTextF = titleElementF.textContent.trim();
      const firstWord = titleTextF.split(" ")[0];
      const spanElementF = document.createElement("span");
      spanElementF.textContent = firstWord;
      spanElementF.classList.add("title");
      titleElementF.innerHTML = titleTextF.replace(
        firstWord,
        spanElementF.outerHTML
      );
    }
  }
});
// lastWord
document.addEventListener("DOMContentLoaded", function () {
  // Fonction pour ajouter le dernier mot dans un span
  function wrapLastWord(className) {
    const elements = document.querySelectorAll(className);
    elements.forEach((element) => {
      const text = element.textContent.trim();
      const words = text.split(" ");
      
      if (words.length > 0) {
        const lastWord = words[words.length - 1];
        const spanElement = document.createElement("span");
        spanElement.textContent = lastWord;
        spanElement.classList.add("title");
        element.innerHTML = text.replace(lastWord, spanElement.outerHTML);
      }
    });
  }
  // Ciblez différentes classes ici
  wrapLastWord(".second .products-section-title");
  wrapLastWord(".panel-title-heading");
});

/************************************************** */
// fonction pour remplacez la classe par "product-item.col-lg-3" dans le page search 
document.addEventListener('DOMContentLoaded', function() {
  // Sélectionnez tous les éléments avec la classe "product-item.col-lg-4"
  var productItems = document.querySelectorAll('.page-search .product-item.col-lg-4');
  // Vérifiez si des éléments ont été trouvés
  if (productItems.length > 0) {
    // Parcourez chaque élément et remplacez la classe par "product-item.col-lg-3"
    productItems.forEach(function(item) {
      item.classList.remove('col-lg-4');
      item.classList.add('col-lg-3');
    });
  }
});

// Fonction pour cocher les cases sur la page de commande
if (document.querySelector("body#checkout .js-customer-form") || 
document.querySelector("body#registration .js-customer-form") || 
document.querySelector("body#identity .js-customer-form")) {
  // Sélectionner les cases à cocher et les cocher
  var maCase = document.querySelector(".psgdpr input");
  var maCases = document.querySelector(".customer_privacy input");
  if (maCase) {
    maCase.checked = true;
  }
  if (maCases) {
    maCases.checked = true;
  }
}
/********************************************************** */
/**
 * Fonction de vérification de la force du mot de passe.
 */
function setupPasswordStrengthCheck() {
  var password = document.getElementById("field-password");
  let lowUpperCase = document.querySelector(".low-upper-case i");
  let number = document.querySelector(".one-number i");
  let passwordStrength = document.getElementById("password-strength");
  let specialChar = document.querySelector(".one-special-char i");
  let eightChar = document.querySelector(".eight-character i");
  let isPasswordValid = false;

  // Get the page elements
  let pagecheckout = document.getElementById("checkout-guest-form");
  let pageregistration = document.getElementById("registration");

  // Get the buttons for each page
  let continueButton = pagecheckout ? pagecheckout.querySelector('button[name="continue"]') : null;
  let enregistrerButton = pageregistration ? pageregistration.querySelector('button[data-link-action="save-customer"]') : null;
  // Get the password strength block
  let passwordStrengthBlock = document.querySelector(".password-strength-block");

 password.addEventListener("keyup", function () {
   let pass = document.getElementById("field-password").value;
   checkStrength(pass);
 });

 function checkStrength(password) {
   let strength = 0;
   // If password contains both lower and uppercase characters
   if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) {
       strength += 1;
       lowUpperCase.classList.remove('fa-times-circle');
       lowUpperCase.classList.add('fa-check');
   } else {
       lowUpperCase.classList.add('fa-times-circle');
       lowUpperCase.classList.remove('fa-check');
   }

   // If it has numbers and characters
    if (password.match(/([0-9])/)) {
       strength += 1;
       number.classList.remove('fa-times-circle');
       number.classList.add('fa-check');
    } else {
       number.classList.add('fa-times-circle');
       number.classList.remove('fa-check');
    }
   // If it has one special character
   if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) {
       strength += 1;
       specialChar.classList.remove('fa-times-circle');
       specialChar.classList.add('fa-check');
   } else {
       specialChar.classList.add('fa-times-circle');
       specialChar.classList.remove('fa-check');
   }

   // If password is greater than 7
   if (password.length > 7) {
       strength += 1;
       eightChar.classList.remove('fa-times-circle');
       eightChar.classList.add('fa-check');
   } else {
       eightChar.classList.add('fa-times-circle');
       eightChar.classList.remove('fa-check');
   }
   // Update the isPasswordValid flag
   isPasswordValid = strength === 4;
 // Enable or disable the appropriate button based on the page
   if (pagecheckout) {
   // Check if it's the checkout page
   continueButton.disabled = !isPasswordValid;
  } else if (pageregistration) {
   // Check if it's the registration page
   enregistrerButton.disabled = !isPasswordValid;
  }
   // Update the password strength bar
   updatePasswordStrengthBar(strength);
   // Show or hide the password strength block based on the page
   if (pagecheckout || pageregistration) {
    passwordStrengthBlock.style.display = "block";
  } else {
    passwordStrengthBlock.style.display = "none";
  }
 }

 function updatePasswordStrengthBar(strength) {
   // Update the password strength bar based on the strength level
   const width = (strength / 4) * 100;
   passwordStrength.style.width = width + '%';
   passwordStrength.className = 'progress-bar';

   if (strength < 2) {
       passwordStrength.classList.add('progress-bar-danger');
   } else if (strength === 2) {
       passwordStrength.classList.add('progress-bar-warning');
   } else if (strength === 3) {
       passwordStrength.classList.add('progress-bar-warning');
   } else if (strength === 4) {
       passwordStrength.classList.add('progress-bar-success');
   }
 }
}
// Call the setupPasswordStrengthCheck function on relevant pages
let pagecheckout = document.getElementById("checkout-guest-form");
let pageregistration = document.getElementById("registration");

if (pagecheckout || pageregistration) {
 setupPasswordStrengthCheck();
}

/*************************************************************** */
// Fonction pour ajouter la classe "product-custum" au class "cart-item".
$(document).ready(function() {
  // Fonction pour ajouter la classe en fonction de la présence de .tabla-resumen
  function addCustomClassToCartItems() {
    $('.cart-item:has(.tabla-resumen)').addClass('product-custum');
  }

  // Appeler la fonction lors du chargement initial
  addCustomClassToCartItems();
  // Sélectionner le conteneur du panier 
  const cartContainer = $('.cart-container')[0];

  if (cartContainer) { // Vérifier si un conteneur de panier a été trouvé
    // Utiliser MutationObserver pour surveiller les changements dans le panier
    const cartObserver = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        addCustomClassToCartItems();
      });
    });
    // Configurer MutationObserver pour surveiller les modifications du panier
    cartObserver.observe(cartContainer, { childList: true, subtree: true });
  }
});

// Fonction pour calculer le poids avec qty
function calculateWeightWithQty() {
  const getElementData = (selector, width, length) => {
    const element = document.querySelector(selector);
    return element ? {
      width,
      length,
      weight: (width / 1000) * (length / 1000) * density * thickness_m,
      volume: (width / 1000) * (length / 1000) * thickness_m,
      surface: (width / 1000) * (length / 1000)
    } : null;
  };

  const setElementValue = (id, value) => {
    const element = document.getElementById(id);
    if (element) element.value = value;
  };

  const setOptionTitle = (selector, text) => {
    const element = document.querySelector(selector);
    if (element) element.textContent = text;
  };

  let thickness_mm = 0;
  let density = 0;

  const productDepthElement = document.getElementById("product_thickness");
  if (productDepthElement) thickness_mm = parseFloat(productDepthElement.value);

  const thickness_m = thickness_mm / 1000;

  const productDensityElement = document.getElementById("product_density");
  if (productDensityElement) density = parseFloat(productDensityElement.value) * 1000;

  const elementsData = [
    getElementData("#resume_price_block_162_0 .option_title", 3050, 2030),
    getElementData("#resume_price_block_162_1 .option_title", 2030, 2030),
    getElementData("#resume_price_block_162_5 .option_title", 2030, 1525),
    getElementData("#resume_price_block_162_6 .option_title", 2030, 1015),
    getElementData("#resume_price_block_162_7 .option_title", 1015, 1015),

    getElementData("#resume_price_block_164_0 .option_title", 3050, 2050),
    getElementData("#resume_price_block_164_1 .option_title", 2050, 1525),

    getElementData("#resume_price_block_165_0 .option_title", 2050, 1550),
    getElementData("#resume_price_block_165_1 .option_title", 1550, 1025),

    getElementData("#resume_price_block_166_0 .option_title", 3050, 2050),
    getElementData("#resume_price_block_166_1 .option_title", 2050, 1525),
    getElementData("#resume_price_block_166_5 .option_title", 2050, 1015),
    getElementData("#resume_price_block_166_6 .option_title", 1020, 1015),

    getElementData("#resume_price_block_167_0 .option_title", 2050, 1250),
    getElementData("#resume_price_block_167_1 .option_title", 1250, 1025),

    getElementData("#resume_price_block_168_0 .option_title", 3050, 2050),
    getElementData("#resume_price_block_168_1 .option_title", 2050, 1525),

    getElementData("#resume_price_block_169_0 .option_title", 3050, 2030),
    getElementData("#resume_price_block_169_1 .option_title", 2030, 1525),
    getElementData("#resume_price_block_169_5 .option_title", 2440, 1220),
    getElementData("#resume_price_block_169_6 .option_title", 3050, 1560),
    getElementData("#resume_price_block_169_7 .option_title", 3050, 1220),
    
    getElementData("#resume_price_block_170_0 .option_title", 3050, 2030),
    getElementData("#resume_price_block_170_1 .option_title", 3050, 1530),
    getElementData("#resume_price_block_170_5 .option_title", 2030, 1525),

    getElementData("#resume_price_block_172_0 .option_title", 1000, 2000),

    getElementData("#resume_price_block_173_0 .option_title", 3000, 2000),
    getElementData("#resume_price_block_173_1 .option_title", 2000, 1500),
    getElementData("#resume_price_block_173_2 .option_title", 2000, 1000),

    getElementData("#resume_price_block_175_0 .option_title", 1200, 2000),
  ].filter(Boolean);

  const weight_kg_s = elementsData.reduce((totalWeight, element) => totalWeight + element.weight, 0);
  const volume_m = elementsData.reduce((totalVolume, element) => totalVolume + element.volume, 0);
  const surface_m = elementsData.reduce((totalsurface, element) => totalsurface + element.surface, 0);

  setElementValue("product_weight", weight_kg_s.toFixed(3));
  setElementValue("product_volume", volume_m.toFixed(5));

  const maxWidth = Math.max(...elementsData.map(element => element.width));
  var width_img  = document.querySelector("#width");
  setElementValue("product_width", maxWidth);
  width_img.innerHTML = "Largeur - "+ maxWidth + " "+"mm";

  const maxLength = Math.max(...elementsData.map(element => element.length));
  var length_img = document.querySelector("#length");
  setElementValue("product_height", maxLength);
  length_img.innerHTML = "Longueur - " +maxLength+ " "+"mm";

  setElementValue("product_depth", 0);
  setElementValue("product_surface", 0);

  setOptionTitle("#resume_tr_poids .option_title", `${weight_kg_s.toFixed(2)} kg`);
  setOptionTitle("#resume_tr_volume .option_title", `${volume_m.toFixed(5)} m³`);
  setOptionTitle("#resume_tr_surface .option_title", `${surface_m.toFixed(3)} m²`);
  setOptionTitle("#resume_tr_epaisseur .option_title", `${thickness_mm} mm`);

  return weight_kg_s;
}
const optIds = ["20", "162", "164", "165", "166", "167", "168", "169", "170", "172", "173" , "175"];
optIds.forEach(optId => {
  const element = document.getElementById(`js_icp_next_opt_${optId}`);
  if (element) {
    element.addEventListener("click", function() {
      setTimeout(calculateWeightWithQty, 1000);
      const image = "/img/idxrcustomproduct/configurations/forms/48.png";
      changeImgCover(image);
      restaurerContenu();
      hideImages()
    });
  }
});

// document.addEventListener("DOMContentLoaded", function () {
//   const navList = document.querySelector(".flex-nav__inner ul");
//   const headings = document.querySelectorAll("#additional-description h2");
//   const navLinks = new Map(); // Associe ID des titres aux liens
//   const OFFSET = 190; // Décalage en pixels (hauteur du menu sticky)

//   // Générer dynamiquement les liens dans le menu
//   headings.forEach((heading, index) => {
//       if (!heading.id) {
//           heading.id = "index" + (index + 1); // Ex: index1, index2...
//       }

//       let title = heading.getAttribute("aria-label") || heading.getAttribute("ariaLabel") || heading.textContent.trim();

//       const listItem = document.createElement("li");
//       const link = document.createElement("a");

//       link.href = "#" + heading.id;
//       link.textContent = title;
//       listItem.appendChild(link);
//       navList.appendChild(listItem);

//       navLinks.set(heading.id, link);
//   });

//   // Gérer l'activation dynamique des liens de navigation
//   function updateActiveLink() {
//       let currentActive = null;

//       // Vérifier quel <h2> est visible et retenir le dernier avant de sortir de l'écran
//       headings.forEach((heading) => {
//           const rect = heading.getBoundingClientRect();
//           if (rect.top <= OFFSET + 10) { // 10px de marge supplémentaire pour éviter les conflits
//               currentActive = heading.id; // On garde le dernier visible
//           }
//       });

//       // Mettre à jour les liens
//       navLinks.forEach((link, id) => {
//           if (id === currentActive) {
//               link.classList.add("active");
//           } else {
//               link.classList.remove("active");
//           }
//       });
//   }

//   // Décalage du scroll pour éviter que le titre soit caché par le menu sticky
//   function smoothScroll(event) {
//       event.preventDefault();
//       const targetId = this.getAttribute("href").substring(1);
//       const targetElement = document.getElementById(targetId);

//       if (targetElement) {
//           const targetPosition = targetElement.getBoundingClientRect().top + window.scrollY - OFFSET;

//           window.scrollTo({
//               top: targetPosition,
//               behavior: "smooth"
//           });

//           // Mettre à jour les classes actives immédiatement après le clic
//           setTimeout(updateActiveLink, 300);
//       }
//   }

//   // Ajouter un écouteur de clic sur chaque lien de navigation
//   navLinks.forEach((link) => {
//       link.addEventListener("click", smoothScroll);
//   });

//   // Écouteur de scroll pour mettre à jour le menu
//   window.addEventListener("scroll", updateActiveLink);

//   // Activer le bon élément au chargement de la page (utile si on charge au milieu de la page)
//   updateActiveLink();
// });


document.addEventListener("DOMContentLoaded", function () {
  const navList = document.querySelector(".flex-nav_home ul");
  const headings = document.querySelectorAll(".section-info h3");
  const navLinks = new Map();
  const OFFSET = 100; 

  let firstLink = null; // Variable pour stocker le premier lien

  // Générer dynamiquement les liens dans le menu
  headings.forEach((heading, index) => {
      if (!heading.id) {
          heading.id = "index" + (index + 1); // Ex: index1, index2...
      }

      let title = heading.textContent.trim();

      const listItem = document.createElement("li");
      const link = document.createElement("a");

      link.href = "#" + heading.id;
      link.textContent = title;
      listItem.appendChild(link);
      navList.appendChild(listItem);

      navLinks.set(heading.id, link);

      // Stocker le premier lien pour l'activer après
      if (index === 0) {
          firstLink = link;
      }
  });

  // Gérer l'activation dynamique des liens de navigation
  function updateActiveLink() {
      let currentActive = null;

      headings.forEach((heading) => {
          const rect = heading.getBoundingClientRect();
          if (rect.top <= OFFSET + 10) { 
              currentActive = heading.id; 
          }
      });

      // Mettre à jour les liens
      navLinks.forEach((link, id) => {
          if (id === currentActive) {
              link.classList.add("active");
          } else {
              link.classList.remove("active");
          }
      });

      // Si aucun élément n'est actif après un scroll, activer le premier par défaut
      if (!currentActive && firstLink) {
          firstLink.classList.add("active");
      }
  }

  // Décalage du scroll pour éviter que le titre soit caché par le menu sticky
  function smoothScroll(event) {
      event.preventDefault();
      const targetId = this.getAttribute("href").substring(1);
      const targetElement = document.getElementById(targetId);

      if (targetElement) {
          const targetPosition = targetElement.getBoundingClientRect().top + window.scrollY - OFFSET;

          window.scrollTo({
              top: targetPosition,
              behavior: "smooth"
          });

          // Mettre à jour les classes actives immédiatement après le clic
          setTimeout(updateActiveLink, 300);
      }
  }

  // Ajouter un écouteur de clic sur chaque lien de navigation
  navLinks.forEach((link) => {
      link.addEventListener("click", smoothScroll);
  });

  // Écouteur de scroll pour mettre à jour le menu
  window.addEventListener("scroll", updateActiveLink);

  // Activer le premier lien immédiatement après le chargement de la page
  if (firstLink) {
      firstLink.classList.add("active");
  }

  // Activer le bon élément au chargement de la page (utile si on charge au milieu de la page)
  updateActiveLink();
});

document.addEventListener('DOMContentLoaded', function () {
  if (!document.body.classList.contains('page-product') && !document.body.classList.contains('page-index')) {
      return; 
  }
  const sampleButton = document.querySelector('.product__group--sample .product-cta--small');
  const destinationBlock = document.querySelector('.wk-sample-block');
  if (sampleButton && destinationBlock) {
      sampleButton.addEventListener('click', function (event) {
          event.preventDefault();
          const offset = 200;
          const elementPosition = destinationBlock.getBoundingClientRect().top + window.scrollY;
          const offsetPosition = elementPosition - offset;
          window.scrollTo({
              top: offsetPosition,
              behavior: 'smooth'
          });
      });
  }
});


document.addEventListener('DOMContentLoaded', function() {
  // Vérifiez si vous êtes sur la page produit
  if (document.body.classList.contains('page-product')) {
      // Sélectionnez le bloc .ets_crosssell_product_page
      var crossSellBlock = document.querySelector('.ets_crosssell_product_page');
      
      // Sélectionnez le conteneur #content-wrapper
      var contentWrapper = document.querySelector('#content-wrapper');
      
      // Vérifiez si les éléments existent
      if (crossSellBlock && contentWrapper) {
          // Sélectionnez le deuxième enfant de #content-wrapper
          var secondChild = contentWrapper.children[1];
          
          // Si un deuxième enfant existe, insérez le bloc avant lui
          if (secondChild) {
              contentWrapper.insertBefore(crossSellBlock, secondChild);
          } else {
              // Sinon, ajoutez-le simplement à la fin
              contentWrapper.appendChild(crossSellBlock);
          }
      }
  }
});

document.addEventListener('DOMContentLoaded', function() {
  if (document.body.classList.contains('page-index')) {
      const sampleBlock = document.querySelector('.wk-sample-block');
      const targetBlock = document.querySelector('.echontillon .elementor-container .elementor-row .elementor-widget-wrap');
      
      if (sampleBlock && targetBlock) {
          targetBlock.appendChild(sampleBlock);
      }
  }
});


/****************************intissar******************** */
//  document.addEventListener('DOMContentLoaded', function() {
//   // Sélection des éléments
//   const carousel = document.querySelector('.text-carousel-inner');
//   const slides = document.querySelectorAll('.text-slide');
//   const dots = document.querySelectorAll('.nav-dot');
//   let currentSlide = 0;
//    const slideCount = slides.length;
//   let slideInterval;
  
//   // Fonction pour aller à un slide spécifique
//   function goToSlide(slideIndex) {
//    if (slideIndex >= slideCount) slideIndex = 0;
//     if (slideIndex < 0) slideIndex = slideCount - 1;
    
//     // Déplacer le carousel
//     carousel.style.transform = `translateX(-${slideIndex * 100}%)`;
    
//      // Mettre à jour la classe active pour les slides et les points
//      slides.forEach((slide) => slide.classList.remove('active'));
//     slides[slideIndex].classList.add('active');
    
//    dots.forEach((dot) => dot.classList.remove('active'));
//    dots[slideIndex].classList.add('active');
    
//     currentSlide = slideIndex;
//   }
  
// //   // Initialiser le premier slide comme actif
//   goToSlide(0);
  
//   // Configurer les écouteurs d'événements pour les points de navigation
//   dots.forEach((dot, index) => {
//    dot.addEventListener('click', () => {
//        goToSlide(index);
//        resetInterval();
//      });
//    });
  
//   // Fonction pour l'autoplay
//   function startAutoplay() {
//      slideInterval = setInterval(() => {
//       goToSlide(currentSlide + 1);
//     }, 5000); // Change de slide toutes les 5 secondes
//   }
  
//    // Réinitialiser l'intervalle après interaction de l'utilisateur
//    function resetInterval() {
//      clearInterval(slideInterval);
//      startAutoplay();
//   }
  
//    // Démarrer l'autoplay
//    startAutoplay();
  
//   // Arrêter l'autoplay quand l'utilisateur survole le carousel
//   carousel.addEventListener('mouseenter', () => {
//     clearInterval(slideInterval);
//   });
  
//    // Reprendre l'autoplay quand l'utilisateur quitte le carousel
//    carousel.addEventListener('mouseleave', () => {
//      startAutoplay();
//   });
//  });
/****************************intissar******************** */
document.addEventListener('DOMContentLoaded', function() {
  // Sélection des éléments
  const carousel = document.querySelector('.text-carousel-inner');
  const slides = document.querySelectorAll('.text-slide');
  const dots = document.querySelectorAll('.nav-dot');

  // Vérifier si les éléments existent
  if (!carousel || slides.length === 0 || dots.length === 0) {
    return; // Arrêter l'exécution si les éléments nécessaires ne sont pas présents
  }

  let currentSlide = 0;
  const slideCount = slides.length;
  let slideInterval;

  // Fonction pour aller à un slide spécifique
  function goToSlide(slideIndex) {
    if (slideIndex >= slideCount) slideIndex = 0;
    if (slideIndex < 0) slideIndex = slideCount - 1;

    // Déplacer le carousel
    carousel.style.transform = `translateX(-${slideIndex * 100}%)`;

    // Mettre à jour la classe active pour les slides et les points
    slides.forEach((slide) => slide.classList.remove('active'));
    slides[slideIndex].classList.add('active');

    dots.forEach((dot) => dot.classList.remove('active'));
    dots[slideIndex].classList.add('active');

    currentSlide = slideIndex;
  }

  // Initialiser le premier slide comme actif
  goToSlide(0);

  // Configurer les écouteurs d'événements pour les points de navigation
  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
      goToSlide(index);
      resetInterval();
    });
  });

  // Fonction pour l'autoplay
  function startAutoplay() {
    slideInterval = setInterval(() => {
      goToSlide(currentSlide + 1);
    }, 5000); // Change de slide toutes les 5 secondes
  }

  // Réinitialiser l'intervalle après interaction de l'utilisateur
  function resetInterval() {
    clearInterval(slideInterval);
    startAutoplay();
  }

  // Démarrer l'autoplay
  startAutoplay();

  // Arrêter l'autoplay quand l'utilisateur survole le carousel
  carousel.addEventListener('mouseenter', () => {
    clearInterval(slideInterval);
  });

  // Reprendre l'autoplay quand l'utilisateur quitte le carousel
  carousel.addEventListener('mouseleave', () => {
    startAutoplay();
  });
});

const toggleLink = document.getElementById("toggleTableLink");
const collapsibleSection = document.getElementById("collapsibleSection");

if (toggleLink && collapsibleSection) {
  toggleLink.addEventListener("click", function(e) {
    e.preventDefault();
    if (collapsibleSection.style.display === "none" || collapsibleSection.style.display === "") {
      collapsibleSection.style.display = "block";
    } else {
      collapsibleSection.style.display = "none";
    }
  });
}

/************************* */
if (window.location.hostname === "decoupe-plexiglass.fr" && document.body.classList.contains('product')) {
    const style = document.createElement('style');
    style.textContent = `
        #group_2 .sr-only.tip {
            height: 55px !important;
            font-size: 12px;
            font-weight: 400;
            padding: 5px 5px 0px;
            line-height: 24px;
        }
    `;
    document.head.appendChild(style);
}
