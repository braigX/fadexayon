{**
* TNT OFFICIAL MODULE FOR PRESTASHOP.
*
* @author    Inetum <inetum.com>
* @copyright 2016-2024 Inetum, 2016-2024 TNT
* @license   https://opensource.org/licenses/MIT MIT License
*}

<head>
    <link rel="stylesheet" type="text/css" href="{TNTOfficiel::getDirModule('css')|escape:'htmlall':'UTF-8'}manifest.css" />
</head>

<div class="body">

<table class="bold">
    <tr>
        <td style="width: 12%;">Compte</td>
        <td style="width: 2%;">:</td>
        <td class="uppercase" style="width: 52%;">{$manifestData['carrierAccount']|escape:'htmlall':'UTF-8'}</td>
        <td style="width: 33%;">&nbsp;</td>
    </tr>
    <tr>
        <td>Nom exp.</td>
        <td>:</td>
        <td class="uppercase">
{$manifestData['address']['name']|escape:'htmlall':'UTF-8'}
        </td>
    </tr>
    <tr><td>Adresse</td>
        <td>:</td>
        <td class="uppercase">
{$manifestData['address']['address1']|escape:'htmlall':'UTF-8'},
{if ($manifestData['address']['address2'] != null)}
{$manifestData['address']['address2']|escape:'htmlall':'UTF-8'},
{/if}
{$manifestData['address']['postcode']|escape:'htmlall':'UTF-8'}{if $manifestData['address']['postcode'] && $manifestData['address']['city']}&nbsp;{/if}
{$manifestData['address']['city']|escape:'htmlall':'UTF-8'}{if $manifestData['address']['city'] && $manifestData['address']['country']},&nbsp;{/if}
{$manifestData['address']['country']|escape:'htmlall':'UTF-8'}</td>
    </tr>
    <tr style="line-height: 13.5pt;"><td></td><td></td><td></td><td></td></tr>
    <tr>
        <td>Nb de colis</td>
        <td>:</td>
        <td class="uppercase">{$manifestData['parcelsNumber']|escape:'htmlall':'UTF-8'}</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>Poids total</td>
        <td>:</td>
        <td class="uppercase">{$manifestData['totalWeight']|escape:'htmlall':'UTF-8'} (Kgs)</td>
        <td>&nbsp;</td>
    </tr>
    <tr><td></td><td></td><td></td><td></td></tr>
</table>

<br />
<div class="hrule">&nbsp;</div><table style="font-size: 8.5pt;">
<tr style="font-weight: bold;background-color: #E0E0E0; line-height: 16pt;"><td style="width: 18%;">Num BT</td><td style="width: 10%">Poids (Kgs)</td><td style="width: 17%">Destinataire</td><td style="width: 7%">CP</td><td style="width: 20%">Ville</td><td style="width: 28%">Service</td></tr>
<tr style="line-height: 3pt;"><td></td><td></td><td></td><td></td><td></td><td></td></tr>
{foreach from=$manifestData['arrParcelInfoList'] item=arrParcelInfoItem}
<tr style="line-height: 6pt;"><td></td><td></td><td></td><td></td><td></td><td></td></tr>
<tr style="line-height: normal;">
    <td>{$arrParcelInfoItem['objTNTParcelModel']->parcel_number|escape:'htmlall':'UTF-8'}</td>
    <td>{$arrParcelInfoItem['objTNTParcelModel']->weight|escape:'htmlall':'UTF-8'}</td>
    <td>{$arrParcelInfoItem['objPSAddressDelivery']->firstname|escape:'htmlall':'UTF-8'} {$arrParcelInfoItem['objPSAddressDelivery']->lastname|escape:'htmlall':'UTF-8'}</td>
    <td>{$arrParcelInfoItem['objPSAddressDelivery']->postcode|escape:'htmlall':'UTF-8'}</td>
    <td>{$arrParcelInfoItem['objPSAddressDelivery']->city|escape:'htmlall':'UTF-8'}</td>
    <td>{$arrParcelInfoItem['strCarrierLabel']|escape:'htmlall':'UTF-8'}</td>
</tr>
{/foreach}
<tr style="line-height: 2pt;"><td></td><td></td><td></td><td></td><td></td><td></td></tr>
</table>
<div class="hspace"><div class="hrule">&nbsp;</div>&nbsp;</div>
<table>
<tr><td><br /><br /><br /><br /><br /></td><td></td><td></td><td></td></tr>
<tr><td>Signature de l'expéditeur</td><td>_____________________</td><td>Date ___/___/______</td><td></td></tr>
<tr><td><br /><br /><br /><br /><br /></td><td></td><td></td><td></td></tr>
<tr><td>Reçu par TNT</td><td>_____________________</td><td>Date ___/___/______</td><td>Heure ____:____</td></tr>
</table>
</div>