<?php
/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/HelperFaq.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/models/OpFaqItem.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/models/OpFaqPage.php';
class InstallSamplesHelper extends HelperFaq
{
    public function installItems()
    {
        $res = true;
        $res &= $this->addOneItem(
            'What is a FAQ item?',
            '<p>A FAQ item is a single pair of question-answer.</p>
            <p>FAQ items are like bricks. You can build a FAQ page from them, you can also use
them for building other FAQ lists, which you can place practically anywhere on
your site, using hooks and shortcodes.</p>
            <p>You can use one FAQ item many times in different lists. (And in different stores, if you have
            multistore enabled.</p>
            <p>You can see all your FAQ items in “All Items” tab. And inside each list you will see FAQ items,
            belonging to that list.</p>'
        );

        $res &= $this->addOneItem(
            'What are FAQ lists?',
            ' <p>Lists of FAQ items can be placed practically everywhere on your site, using hooks
and shortcodes. In this module you can choose on which exact pages your list of
FAQ items can be shown.</p>
            <p>On each list’s edit page you can see a list of FAQ items (a new list should first be
saved). You can add existing items,
create a new one, change their positions or remove items from list without
deleting from shop.</p>'
        );

        $res &= $this->addOneItem(
            'How to add FAQ items to lists?',
            ' <p>FAQ items are added to FAQ page or lists on page’s and lists’ edit pages. 
            As well as on item’s edit page, too. </p>'
        );

        $res &= $this->addOneItem(
            'How to use Google Schema Markup?',
            '<p>In this module JSON-LD format of FAQ schema markup is used. 
This markup is not interleaved with the user-visible text, it’s lightweight and recommended for use
by Google.</p>
<p>Be careful, do not use FAQ schema markup on the same page twice. Or with another type of schema markup. 
E.g. you should remove unnecessary product markup from category pages.</p>'
        );

        return $res;
    }

    protected function addOneItem($question, $answer)
    {
        $languages = Language::getLanguages(false);

        $faqItem = new OpFaqItem($this->module);

        foreach ($languages as $language) {
            $faqItem->answer[$language['id_lang']] = $answer;
            $faqItem->question[$language['id_lang']] = $question;
            $faqItem->title[$language['id_lang']] = $this->setItemTitle($question);
        }
        $res = $faqItem->add();
        $res &= $this->addItemToAllPages($faqItem->id);

        return $res;
    }

    public function installPage()
    {
        $shops = $this->getAllShops();

        $res = true;

        $title = 'Frequently Asked Questions';
        $description = "FAQ is an acronym for Frequently Asked 
Questions. It is also sometimes used as the singular Frequently Asked Question (Although when was the last time you 
heard only one question?). Some have called it Frequently Answered Questions as well. This isn't necessarily 
correct, but it isn't necessarily wrong either. It effectively has the same meaning.";
        foreach ($shops as $id_shop) {
            $res &= $this->createOrUpdatePage(
                $id_shop,
                1,
                1,
                1,
                0,
                $title,
                $description,
                'h1'
            );
        }

        return $res;
    }

    public function addItemToAllPages($id)
    {
        $res = true;
        $shops = $this->getAllShops();
        foreach ($shops as $id_shop) {
            $res &= $this->module->rep->addItemToPage($id, $id_shop);
        }

        return $res;
    }

    protected function getAllShops()
    {
        return Shop::getShops(false, null, true);
    }
}
