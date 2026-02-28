{*
* 2018 GEODIS
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    GEODIS <contact@geodis.com>
*  @copyright 2018 GEODIS
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<li class="geodisRelay" data-values-var="code" data-set-class="setPointClass" data-parent-var="idCarrier">
    <span class="geodisRelay__distance" data-var="distance"></span>
    <span class="geodisRelay__name" data-var="name"></span>
    <button class="geodisRelay__action" data-action="selectPoint" data-values-var="code" data-parent-var="idCarrier"class="geodisRelay__action">
        <span class="geodisRelay__actionSelect">{__ s='front.popin.point.choose'}</span>
        <span class="geodisRelay__actionSelected">{__ s='front.popin.point.selected'}</span>
    </button>
    <span class="geodisRelay__address1" data-var="address1"></span>
    <span class="geodisRelay__address2" data-var="address2"></span>
    <span class="geodisRelay__zipcode" data-var="zipCode"></span>
    <span class="geodisRelay__city" data-var="city"></span>
    <div class="geodisRelay__actionSeeTimetable" data-action="displayTimetable">{__ s='front.popin.point.displayTimetable'}</div>
    <ul class="geodisTimetable">
        <li class="geodisTimetable__line">
            <span class="geodisTimetable__title">{__ s='front.popin.point.timetable.title.monday'}</span>
            <span class="geodisTimetable__value" data-render="renderTimeline" data-values-var="monday"></span>
        </li>
        <li class="geodisTimetable__line">
            <span class="geodisTimetable__title">{__ s='front.popin.point.timetable.title.tuesday'}</span>
            <span class="geodisTimetable__value" data-render="renderTimeline" data-values-var="tuesday"></span>
        </li>
        <li class="geodisTimetable__line">
            <span class="geodisTimetable__title">{__ s='front.popin.point.timetable.title.wednesday'}</span>
            <span class="geodisTimetable__value" data-render="renderTimeline" data-values-var="wednesday"></span>
        </li>
        <li class="geodisTimetable__line">
            <span class="geodisTimetable__title">{__ s='front.popin.point.timetable.title.thursday'}</span>
            <span class="geodisTimetable__value" data-render="renderTimeline" data-values-var="thursday"></span>
        </li>
        <li class="geodisTimetable__line">
            <span class="geodisTimetable__title">{__ s='front.popin.point.timetable.title.friday'}</span>
            <span class="geodisTimetable__value" data-render="renderTimeline" data-values-var="friday"></span>
        </li>
        <li class="geodisTimetable__line">
            <span class="geodisTimetable__title">{__ s='front.popin.point.timetable.title.saturday'}</span>
            <span class="geodisTimetable__value" data-render="renderTimeline" data-values-var="saturday"></span>
        </li>
        <li class="geodisTimetable__line">
            <span class="geodisTimetable__title">{__ s='front.popin.point.timetable.title.sunday'}</span>
            <span class="geodisTimetable__value" data-render="renderTimeline" data-values-var="sunday"></span>
        </li>
    </ul>
</li>
