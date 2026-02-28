
function refreshFeeLine() {

  const randomParam = Math.random(); // Add random number to URL
  const urlWithRand = minCartFeeUrl + (minCartFeeUrl.includes('?') ? '&' : '?') + 'rand=' + randomParam;

  fetch(urlWithRand)
    .then(r => r.json())
    .then(data => {
      // Remove all existing fee lines
      document.querySelectorAll('.cart-summary-line.min-cart-fee, tr.min-cart-fee-row').forEach(e => e.remove());

      // If no fee, skip insertion
      if (!data.fee_incl_tax || data.fee_incl_tax <= 0) {
        return;
      }

      // Prepare the DIV version for cart summary (shipping section)
      const feeDivHTML = `
        <div class="cart-summary-line min-cart-fee  cart-summary-subtotals">
          <span class="label">${minCartFeeLabel}</span>
          <span class="value">${data.formatted}</span>
        </div>
      `;

      // Prepare the TR version for tax rows
      const feeTrHTML = `
        <tr class="min-cart-fee-row">
          <td class="label"></td>
          <td class="value" colspan="1">${minCartFeeLabel}: ${data.formatted}</td>
        </tr>
      `;  

      const feeShippingHTML = `
        <div class="row min-cart-fee-row">
          <div class="col-6 col-xs-6">${minCartFeeLabel}</div>
          <div class="col-6 col-xs-6  text-right text-xs-right">${data.formatted}</div>
        </div>
      `;

        document
        .querySelectorAll('.priceproductshipping')
        .forEach(container => {
          // Find the exact “Livraison” row
          container.querySelectorAll('.row').forEach(row => {
            const labelCell = row.querySelector('.col-6');
            if (labelCell && labelCell.textContent.trim().startsWith('Livraison')) {
              row.insertAdjacentHTML('afterend', feeShippingHTML);
            }
          });
        });
      // Insert after all #cart-subtotal-shipping blocks
      const shippingBlocks = document.querySelectorAll('[id="cart-subtotal-shipping"]');
      if (shippingBlocks.length) {
        shippingBlocks.forEach(el => el.insertAdjacentHTML('afterend', feeDivHTML));
      }

      // Insert after all .sub.taxes <tr> rows
      const taxRows = document.querySelectorAll('tr.sub.taxes');
      if (taxRows.length) {
        taxRows.forEach(tr => tr.insertAdjacentHTML('afterend', feeTrHTML));
      }
    })
    .catch(error => {
      // console.error('[MinimumCartFee] Error fetching fee data:', error);
    });
}

;(function () {
  // if (typeof minCartFee === 'undefined' || minCartFee <= 0) {
  //   return;
  // }

  document.addEventListener('DOMContentLoaded', () => {
    setTimeout(refreshFeeLine, 500);
  });

  if (typeof prestashop !== 'undefined' && prestashop.on) {
    prestashop.on('updatedCart', () => {
      setTimeout(refreshFeeLine, 1000);
    });
  }

  document.body.addEventListener('change', function (e) {
    if (e.target.name && e.target.name.indexOf('delivery_option[') === 0) {
      setTimeout(refreshFeeLine, 1000);
    }
  });

  // if (typeof jQuery !== 'undefined') {
  //   $(document).ajaxComplete(() => {
  //     setTimeout(refreshFeeLine, 700);
  //   });
  // }
})();
