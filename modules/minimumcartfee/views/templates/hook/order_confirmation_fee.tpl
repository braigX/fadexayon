{literal}
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const totalRow = document.querySelector('tr.total-value');
    if (!totalRow) return;

    const customRow = document.createElement('tr');
    customRow.innerHTML = `
      <td>{/literal}{$custom_fee_label|escape:'htmlall':'UTF-8'}{literal}</td>
      <td>{/literal}{$custom_fee_amount nofilter}{literal}</td>
    `;

    totalRow.parentNode.insertBefore(customRow, totalRow);
  });
</script>
{/literal}
