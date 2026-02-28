<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class EtsSeoTranslation
{
    public static function dataTrans()
    {
        return [
            'outbound_link' => [
                'en' => 'Outbound links are links from your website to other websites. Outbound links help search engines to find websites and how webpages relate to each other. You should include at least an outbound link into your content to increase the trust of your content.',
                'fr' => 'Les liens sortants sont des liens de votre site Web vers d\'autres sites Web. Les liens sortants aident les moteurs de recherche à trouver des sites Web et comment les pages Web sont liées les unes aux autres. Vous devez inclure au moins un lien sortant dans votre contenu pour augmenter la confiance de votre contenu.',
                'es' => 'Los enlaces salientes son enlaces desde su sitio web a otros sitios web. Los enlaces salientes ayudan a los motores de búsqueda a encontrar sitios web y cómo las páginas web se relacionan entre sí. Debe incluir al menos un enlace saliente en su contenido para aumentar la confianza de su contenido.',
                'it' => 'I link uscenti sono link dal tuo sito Web ad altri siti Web. Link uscenti aiutano i motori di ricerca a trovare siti Web e il modo in cui le pagine Web sono in relazione tra loro. Dovresti includere almeno un link in uscita nei tuoi contenuti per aumentare la fiducia dei tuoi contenuti.',
                'pl' => 'Linki wychodzące to linki z Twojej strony internetowej do innych stron. Linki wychodzące pomagają wyszukiwarkom znaleźć strony internetowe i zrozumieć, jak strony internetowe są powiązane ze sobą. Powinieneś dodać co najmniej jeden link wychodzący do swojej treści, aby zwiększyć jej wiarygodność.',
                'ru' => 'Внешние ссылки — это ссылки с вашего сайта на другие сайты. Внешние ссылки помогают поисковым системам находить сайты и понимать, как веб-страницы связаны друг с другом. Вы должны включить хотя бы одну внешнюю ссылку в ваш контент, чтобы повысить его доверие.',
                'pt' => 'Links externos são links do seu site para outros sites. Links externos ajudam os motores de busca a encontrar sites e a entender como as páginas da web estão relacionadas entre si. Você deve incluir pelo menos um link externo no seu conteúdo para aumentar sua credibilidade.',
                'de' => 'Ausgehende Links sind Links von Ihrer Website zu anderen Websites. Ausgehende Links helfen Suchmaschinen dabei, Websites zu finden und zu verstehen, wie Webseiten miteinander in Beziehung stehen. Sie sollten mindestens einen ausgehenden Link in Ihren Inhalt einfügen, um das Vertrauen in Ihren Inhalt zu erhöhen.',
                'cs' => 'Odchozí odkazy jsou odkazy z vaší webové stránky na jiné webové stránky. Odchozí odkazy pomáhají vyhledávačům najít webové stránky a pochopit, jak spolu stránky souvisejí. Do svého obsahu byste měli zahrnout alespoň jeden odchozí odkaz, abyste zvýšili důvěryhodnost svého obsahu.',
                'nl' => 'Uitgaande links zijn links van uw website naar andere websites. Uitgaande links helpen zoekmachines om websites te vinden en te begrijpen hoe webpagina\'s met elkaar verbonden zijn. U dient ten minste één uitgaande link in uw inhoud op te nemen om het vertrouwen in uw inhoud te vergroten.',
            ],
            'internal_link' => [
                'en' => 'An internal link is any link from one page on your website to another page on your website. Internal links help Google find, index and understand all of your site\'s pages. You should insert at least an internal link (followed link is preferred) into your content.',
                'fr' => 'Un lien interne est un lien d\'une page de votre site Web vers une autre page de votre site Web. Les liens internes aident Google à trouver, indexer et comprendre toutes les pages de votre site. Vous devez insérer au moins un lien interne (le lien suivi est préférable) dans votre contenu.',
                'es' => 'Un enlace interno es cualquier enlace de una página en su sitio web a otra página en su sitio web. Los enlaces internos ayudan a Google a encontrar, indexar y comprender todas las páginas de su sitio. Debe insertar al menos un enlace interno (se prefiere el enlace seguido) en su contenido.',
                'it' => 'Link interni è qualsiasi link da una pagina del sito Web a un\'altra pagina del sito Web. Link interni aiutano Google a trovare, indicizzare e comprendere tutte le pagine del tuo sito. È necessario inserire almeno un link interno (è preferibile il link seguito) nel contenuto.',
                'pl' => 'Link wewnętrzny to każdy link z jednej strony Twojej witryny do innej strony w tej samej witrynie. Linki wewnętrzne pomagają Google znaleźć, zaindeksować i zrozumieć wszystkie strony Twojej witryny. Powinieneś dodać co najmniej jeden link wewnętrzny (preferowany jest link śledzony) do swojej treści.',
                'ru' => 'Внутренняя ссылка — это любая ссылка с одной страницы вашего сайта на другую страницу этого же сайта. Внутренние ссылки помогают Google находить, индексировать и понимать все страницы вашего сайта. Вам следует вставить хотя бы одну внутреннюю ссылку (предпочтительно со статусом «следовать») в ваш контент.',
                'pt' => 'Um link interno é qualquer link de uma página no seu site para outra página no mesmo site. Links internos ajudam o Google a encontrar, indexar e entender todas as páginas do seu site. Você deve inserir pelo menos um link interno (link seguido é preferido) no seu conteúdo.',
                'de' => 'Ein interner Link ist jeder Link von einer Seite auf Ihrer Website zu einer anderen Seite auf Ihrer Website. Interne Links helfen Google, alle Seiten Ihrer Website zu finden, zu indexieren und zu verstehen. Sie sollten mindestens einen internen Link (bevorzugt als Follow-Link) in Ihren Inhalt einfügen.',
                'cs' => 'Interní odkaz je jakýkoli odkaz z jedné stránky na vašem webu na jinou stránku vašeho webu. Interní odkazy pomáhají Googlu najít, indexovat a pochopit všechny stránky vašeho webu. Do svého obsahu byste měli vložit alespoň jeden interní odkaz (preferovaný je následný odkaz).',
                'nl' => 'Interne links zijn links van de ene pagina op uw website naar een andere pagina op uw website. Interne links helpen Google om alle pagina\'s van uw site te vinden, te indexeren en te begrijpen. U dient ten minste één interne link (bij voorkeur een follow-link) in uw inhoud op te nemen.',
            ],
            'keyphrase_length' => [
                'en' => 'The focus keyphrase is the phrase for which you want your page to be found for. For your focus keyphrase, we suggest a limit of 4 words.',
                'fr' => 'La phrase-clé principal est la phrase pour laquelle vous souhaitez que votre page soit trouvée. Pour votre phrase-clé principal, nous suggérons une limite de 4 mots.',
                'es' => 'La frase clave central es la frase para la que desea que se encuentre su página. Para su frase clave central, sugerimos un límite de 4 palabras.',
                'it' => 'Frase chiave principale è la frase per la quale si desidera trovare la pagina. Per la frase chiave principale, suggeriamo un limite di 4 parole.',
                'pl' => 'Główna fraza kluczowa to fraza, dla której chcesz, aby Twoja strona była odnaleziona. Dla głównej frazy kluczowej sugerujemy limit 4 słów.',
                'ru' => 'Ключевая фраза — это фраза, по которой вы хотите, чтобы вашу страницу находили. Для ключевой фразы мы рекомендуем ограничение в 4 слова.',
                'pt' => 'A frase-chave principal é a frase pela qual você deseja que sua página seja encontrada. Para a sua frase-chave principal, sugerimos um limite de 4 palavras.',
                'de' => 'Das Fokus-Schlüsselwort ist der Begriff, unter dem Ihre Seite gefunden werden soll. Für Ihr Fokus-Schlüsselwort empfehlen wir eine Begrenzung auf 4 Wörter.',
                'cs' => 'Hlavní klíčové slovo je fráze, podle které chcete, aby byla vaše stránka nalezena. U hlavního klíčového slova doporučujeme omezit délku na 4 slova.',
                'nl' => 'De focuszoekterm is de term waarop u wilt dat uw pagina wordt gevonden. Voor uw focuszoekterm raden wij aan om deze te beperken tot maximaal 4 woorden.',
            ],
            'keyphrase_in_title' => [
                'en' => 'In the meta title you should always try using an exact match of your focus keyphrase. Try to keep the meta title original, concentrated and simple while adding your focus keyphrase in a suitable position.',
                'fr' => 'Dans le méta-titre, vous devez toujours essayer d\'utiliser une correspondance exacte de votre phrase-clé principal. Essayez de garder le méta-titre original, concentré et simple tout en ajoutant votre phrase-clé principal dans une position appropriée.',
                'es' => 'En el meta título, siempre debes intentar usar una coincidencia exacta de tu frase clave central. Intente mantener el meta título original, concentrado y simple mientras agrega su frase clave central en una posición adecuada.',
                'it' => 'Nel meta titolo dovresti sempre provare a usare una corrispondenza esatta della frase chiave principale. Cerca di mantenere il meta titolo originale, concentrato e semplice aggiungendo la frase chiave principale in una posizione adatta.',
                'pl' => 'W meta tytule zawsze powinieneś starać się używać dokładnego dopasowania głównej frazy kluczowej. Staraj się, aby meta tytuł był oryginalny, zwięzły i prosty, umieszczając główną frazę kluczową w odpowiednim miejscu.',
                'ru' => 'В мета-заголовке вы всегда должны стараться использовать точное совпадение вашей ключевой фразы. Постарайтесь сохранить мета-заголовок оригинальным, лаконичным и простым, добавляя ключевую фразу в подходящее место.',
                'pt' => 'No meta título, você deve sempre tentar usar uma correspondência exata da sua frase-chave principal. Tente manter o meta título original, concentrado e simples, adicionando sua frase-chave principal em uma posição adequada.',
                'de' => 'Im Meta-Titel sollten Sie stets eine exakte Übereinstimmung Ihres Fokus-Schlüsselworts verwenden. Halten Sie den Meta-Titel originell, prägnant und einfach, während Sie das Fokus-Schlüsselwort an einer geeigneten Stelle hinzufügen.',
                'cs' => 'V meta titulu byste se měli vždy snažit použít přesnou shodu hlavního klíčového slova. Udržujte meta titul originální, stručný a jednoduchý a přidejte hlavní klíčové slovo na vhodné místo.',
                'nl' => 'In de metatitel moet u altijd proberen een exacte match van uw focuszoekterm te gebruiken. Probeer de metatitel origineel, beknopt en eenvoudig te houden terwijl u uw focuszoekterm op een geschikte plaats toevoegt.',
            ],
            'keyphrase_in_page_title' => [
                'en' => 'In the [page_title] you should always try using an exact match of your focus keyphrase. Try to keep the page title original, concentrated and simple while adding your focus keyphrase in a suitable position.',
                'fr' => 'Dans la [page_title] vous devez toujours essayer d\'utiliser une correspondance exacte de votre phrase-clé principal. Essayez de garder le titre de la page original, concentré et simple tout en ajoutant votre phrase-clé principal dans une position appropriée.',
                'es' => 'En [page_title] siempre debe intentar usar una coincidencia exacta de su frase clave central. Intente mantener el título de la página original, concentrado y simple mientras agrega su frase clave central en una posición adecuada.',
                'it' => 'Nel [page_title] dovresti sempre provare a usare una corrispondenza esatta della frase chiave principale. Cerca di mantenere il titolo della pagina originale, concentrato e semplice aggiungendo la frase chiave principale in una posizione adatta.',
                'pl' => 'W [page_title] zawsze powinieneś starać się używać dokładnego dopasowania głównej frazy kluczowej. Staraj się, aby tytuł strony był oryginalny, zwięzły i prosty, umieszczając główną frazę kluczową w odpowiednim miejscu.',
                'ru' => 'В [page_title] вы всегда должны стараться использовать точное совпадение вашей ключевой фразы. Постарайтесь сохранить заголовок страницы оригинальным, лаконичным и простым, добавляя ключевую фразу в подходящее место.',
                'pt' => 'No [page_title], você deve sempre tentar usar uma correspondência exata da sua frase-chave principal. Tente manter o título da página original, concentrado e simples, adicionando sua frase-chave principal em uma posição adequada.',
                'de' => 'Im [page_title] sollten Sie stets eine exakte Übereinstimmung Ihres Fokus-Schlüsselworts verwenden. Halten Sie den Seitentitel originell, prägnant und einfach, während Sie das Fokus-Schlüsselwort an einer geeigneten Stelle hinzufügen.',
                'cs' => 'V [page_title] byste se měli vždy snažit použít přesnou shodu hlavního klíčového slova. Udržujte název stránky originální, stručný a jednoduchý a přidejte hlavní klíčové slovo na vhodné místo.',
                'nl' => 'In de [page_title] moet u altijd proberen een exacte match van uw focuszoekterm te gebruiken. Houd de paginatitel origineel, beknopt en eenvoudig en voeg uw focuszoekterm op een geschikte plaats toe.',
            ],
            'page_title_length' => [
                'en' => 'Your [page_title] should not exceed 65 characters.',
                'fr' => 'Le titre de votre [page_title] ne doit pas dépasser 65 caractères.',
                'es' => 'Tu [page_title] no debe exceder los 65 caracteres.',
                'it' => 'Il tuo [page_title] non deve superare i 65 caratteri.',
                'pl' => 'Twój [page_title] nie powinien przekraczać 65 znaków.',
                'ru' => 'Ваш [page_title] не должен превышать 65 символов.',
                'pt' => 'O seu [page_title] não deve exceder 65 caracteres.',
                'de' => 'Ihr [page_title] sollte 65 Zeichen nicht überschreiten.',
                'cs' => 'Váš [page_title] by neměl překročit 65 znaků.',
                'nl' => 'Uw [page_title] mag niet langer zijn dan 65 tekens.',
            ],

            'minor_keyphrase_length' => [
                'en' => 'Related keyphrase is a phrase closely related to the focus keyphrase, usually long, descriptive and may not have a high level of traffic. You can enter multiple related keyphrases, each keyphrase should not exceed 4 words.',
                'fr' => 'La phrase-clé associée est une phrase étroitement liée à la phrase-clé principal, généralement longue, descriptive et peut ne pas avoir un niveau de trafic élevé. Vous pouvez saisir plusieurs phrases-clés associées, chaque phrase-clé ne doit pas dépasser 4 mots.',
                'es' => 'La frase clave relacionada es una frase estrechamente relacionada con la frase clave central, generalmente larga, descriptiva y puede no tener un alto nivel de tráfico. Puede ingresar múltiples frases clave relacionadas, cada frase clave no debe exceder las 4 palabras.',
                'it' => 'La frase chiave correlata è una frase strettamente correlata alla frase chiave principale, generalmente lunga, descrittiva e potrebbe non avere un livello elevato di traffico. È possibile inserire più frasi chiave correlate, ogni frase chiave non deve superare le 4 parole.',
                'de' => 'Das verwandte Schlüsselwort ist ein eng mit dem Fokus-Schlüsselwort verbundenes Wort oder eine Phrase, die in der Regel lang, beschreibend ist und möglicherweise kein hohes Verkehrsaufkommen hat. Sie können mehrere verwandte Schlüsselwörter eingeben, jedes Schlüsselwort sollte 4 Wörter nicht überschreiten.',
                'pl' => 'Powiązana fraza kluczowa to fraza ściśle powiązana z główną frazą kluczową, zazwyczaj długa, opisowa i może nie generować dużego ruchu. Możesz wprowadzić wiele powiązanych fraz kluczowych, każda z nich nie powinna przekraczać 4 słów.',
                'cs' => 'Související klíčová fráze je fráze úzce související s hlavní klíčovou frází, obvykle dlouhá, popisná a nemusí mít vysokou úroveň provozu. Můžete zadat více souvisejících klíčových frází, každá fráze by neměla přesáhnout 4 slova.',
                'pt' => 'A frase-chave relacionada é uma frase intimamente relacionada à frase-chave principal, geralmente longa, descritiva e que pode não ter um alto nível de tráfego. Pode inserir várias frases-chave relacionadas, cada uma não deve exceder 4 palavras.',
                'nl' => 'Het gerelateerde trefwoord is een woordgroep die nauw verwant is aan het focus trefwoord, meestal lang, beschrijvend en mogelijk niet veel verkeer genereert. U kunt meerdere gerelateerde trefwoorden invoeren, elk trefwoord mag niet langer zijn dan 4 woorden.',
                'ru' => 'Связанная ключевая фраза — это фраза, тесно связанная с основной ключевой фразой, обычно длинная, описательная и, возможно, не имеет высокого уровня трафика. Вы можете ввести несколько связанных ключевых фраз, каждая из которых не должна превышать 4 слова.',
            ],
            'keyphrase_in_subheading' => [
                'en' => 'You should include your focus keyphrase in subheadings to show readers what the specific subparts of the text are talking about, this is called a subheading reflects the topic. In general, a subheading is considered to reflect the topic if at least half of the words from your focus keyphrase are used in it. The focus keyphrase should appear in 30 to 75% of your H2 and H3 subheadings.',
                'fr' => 'Vous devez inclure votre phrase-clé principal dans les sous-titres pour montrer aux lecteurs de quoi parlent les sous-parties spécifiques du texte, c\'est ce qu\'on appelle un sous-titre qui reflète le sujet. En général, un sous-titre est considéré comme reflétant le sujet si au moins la moitié des mots de votre phrase-clé principal y sont utilisés. La phrase-clé principal doit apparaître dans 30 à 75% de vos sous-titres H2 et H3.',
                'es' => 'Debe incluir su frase clave central en los subtítulos para mostrar a los lectores de qué están hablando las subpartes específicas del texto, esto se llama un subtítulo que refleja el tema. En general, se considera que un subtítulo refleja el tema si se usa al menos la mitad de las palabras de su frase clave central. La frase clave central debería aparecer en 30 a 75% de su subtítulos H2 y H3.',
                'it' => 'Dovresti includere la frase chiave principale nei sottovoci per mostrare ai lettori di cosa parlano le specifiche sottoparti del testo, questo si chiama sottovoci che riflette l\'argomento. In generale, si considera che un sottovoce rifletta l\'argomento se almeno la metà delle parole della frase chiave principale viene utilizzata in esso. La frase chiave principale dovrebbe apparire nel 30-75% delle sottovoci H2 e H3.',
                'de' => 'Sie sollten Ihr Fokus-Schlüsselwort in Unterüberschriften einfügen, um den Lesern zu zeigen, worum es in den spezifischen Unterabschnitten des Textes geht. Dies wird als Unterüberschrift bezeichnet, die das Thema widerspiegelt. Im Allgemeinen wird eine Unterüberschrift als themenbezogen angesehen, wenn mindestens die Hälfte der Wörter aus Ihrem Fokus-Schlüsselwort verwendet wird. Das Fokus-Schlüsselwort sollte in 30 bis 75 % Ihrer H2- und H3-Unterüberschriften erscheinen.',
                'pl' => 'Powinieneś umieścić główną frazę kluczową w podtytułach, aby pokazać czytelnikom, o czym mówią poszczególne podsekcje tekstu. Jest to nazywane podtytułem odzwierciedlającym temat. Generalnie podtytuł jest uznawany za odzwierciedlający temat, jeśli użyto w nim co najmniej połowy słów z głównej frazy kluczowej. Główna fraza kluczowa powinna pojawiać się w 30 do 75% podtytułów H2 i H3.',
                'cs' => 'Měli byste zahrnout svou hlavní klíčovou frázi do podnadpisů, aby čtenáři věděli, o čem jednotlivé části textu hovoří. To se nazývá podnadpis odrážející téma. Obecně platí, že podnadpis je považován za odrážející téma, pokud je v něm použita alespoň polovina slov z vaší hlavní klíčové fráze.',
                'pt' => 'Você deve incluir sua frase-chave principal em subtítulos para mostrar aos leitores sobre o que as subpartes específicas do texto estão falando, isso é chamado de subtítulo que reflete o tópico. Em geral, um subtítulo é considerado refletir o tópico se pelo menos metade das palavras de sua frase-chave principal for usada nele. A frase-chave principal deve aparecer em 30 a 75% de seus subtítulos H2 e H3.',
                'nl' => 'U moet uw focus trefwoord opnemen in subkoppen om lezers te laten zien waar de specifieke subonderdelen van de tekst over gaan. Dit wordt een subkop genoemd die het onderwerp weerspiegelt. Over het algemeen wordt een subkop beschouwd als weerspiegeling van het onderwerp als ten minste de helft van de woorden uit uw focus trefwoord wordt gebruikt. Het focus trefwoord moet voorkomen in 30 tot 75% van uw H2- en H3-subkoppen.',
                'ru' => 'Вы должны включить ключевую фразу в подзаголовки, чтобы показать читателям, о чем говорят конкретные части текста. Это называется подзаголовком, который отражает тему. Как правило, подзаголовок считается отражающим тему, если в нем используется хотя бы половина слов из вашей ключевой фразы. Ключевая фраза должна появляться в 30-75% ваших подзаголовков H2 и H3.',
            ],

            'keyphrase_in_intro' => [
                'en' => 'Google uses your introduction (normally it is the first paragraph of your text) to decide what your text is about. In this introduction, you should use your focus keyphrase. It would be better if you add focus keyphrase right in the first sentence. In case of product page, the introduction will be the product summary.',
                'fr' => 'Google utilise votre introduction (normalement c\'est le premier paragraphe de votre texte) pour décider de quoi parle votre texte. Dans cette introduction, vous devez utiliser votre phrase-clé principal. Il serait préférable d\'ajouter la phrase-clé principal directement dans la première phrase. En cas de page produit, l\'introduction sera le résumé du produit.',
                'es' => 'Google usa su introducción (normalmente es el primer párrafo de su texto) para decidir de qué trata su texto. En esta introducción, debe usar su frase clave central. Sería mejor si agrega la frase clave central directamente en la primera oración. En el caso de la página del producto, la introducción será el resumen del producto.',
                'it' => 'Google usa la tua introduzione (normalmente è il primo paragrafo del tuo testo) a decidere di cosa tratta il tuo testo. In questa introduzione, è necessario utilizzare la frase chiave principale. Sarebbe meglio se aggiungi la frase chiave principale proprio nella prima frase. Nel caso della pagina del prodotto, l\'introduzione sarà il riepilogo del prodotto.',
                'de' => 'Google verwendet Ihre Einleitung (normalerweise ist dies der erste Absatz Ihres Textes), um zu entscheiden, worum es in Ihrem Text geht. In dieser Einleitung sollten Sie Ihr Fokus-Schlüsselwort verwenden. Es wäre besser, wenn Sie das Fokus-Schlüsselwort direkt im ersten Satz hinzufügen. Im Falle einer Produktseite ist die Einleitung die Produktzusammenfassung.',
                'pl' => 'Google używa wprowadzenia (zazwyczaj jest to pierwszy akapit tekstu), aby określić, o czym jest Twój tekst. W tym wprowadzeniu powinieneś użyć głównej frazy kluczowej. Lepiej byłoby, gdybyś dodał główną frazę kluczową już w pierwszym zdaniu. W przypadku strony produktu wprowadzenie będzie podsumowaniem produktu.',
                'cs' => 'Google používá váš úvod (obvykle je to první odstavec vašeho textu), aby rozhodl, o čem je váš text. V tomto úvodu byste měli použít svou hlavní klíčovou frázi. Bylo by lepší, kdybyste hlavní klíčovou frázi přidali hned do první věty. V případě stránky produktu bude úvod shrnutím produktu.',
                'pt' => 'O Google utiliza sua introdução (normalmente é o primeiro parágrafo do seu texto) para decidir sobre o que é seu texto. Nesta introdução, você deve usar sua frase-chave principal. Seria melhor se você adicionasse a frase-chave principal logo na primeira frase. No caso de uma página de produto, a introdução será o resumo do produto.',
                'nl' => 'Google gebruikt uw inleiding (meestal de eerste alinea van uw tekst) om te bepalen waar uw tekst over gaat. In deze inleiding moet u uw focus trefwoord gebruiken. Het is beter om het focus trefwoord direct in de eerste zin toe te voegen. In het geval van een productpagina zal de inleiding de samenvatting van het product zijn.',
                'ru' => 'Google использует ваше введение (обычно это первый абзац вашего текста), чтобы определить, о чем ваш текст. В этом введении вы должны использовать ключевую фразу. Лучше всего добавить ключевую фразу прямо в первое предложение. В случае страницы продукта введение будет кратким описанием продукта.',
            ],
            'keyphrase_density' => [
                'en' => 'Focus keyphrase density is the number of times your focus keyphrase appears in your text, compared to the total text of that page. We recommend that you maintain a focus keyphrase density of 0.3% to 3% (but at least not less than 3 times).',
                'fr' => 'La densité de phrase-clé principal est le nombre de fois que votre phrase-clé principal apparaît dans votre texte, par rapport au texte total de cette page. Nous vous recommandons de maintenir une densité de phrase-clé principal de 0.3% à 3% (mais au moins pas moins de 3 fois).',
                'es' => 'Densidad de la frase clave central es la cantidad de veces que su frase clave central aparece en su texto, en comparación con el texto total de esa página. Recomendamos que mantenga una densidad de frase clave central de 0.3% a 3% (pero al menos no menos de 3 veces).',
                'it' => 'La densità della frase chiave principale è il numero di volte in cui la frase chiave principale appare nel testo, rispetto al testo totale di quella pagina. Ti consigliamo di mantenere una densità della frase chiave principale compresa tra 0.3% e 3% (ma almeno non meno di 3 volte).',
                'de' => 'Die Dichte des Fokus-Schlüsselworts gibt an, wie oft Ihr Fokus-Schlüsselwort in Ihrem Text im Verhältnis zum gesamten Text dieser Seite erscheint. Wir empfehlen eine Fokus-Schlüsselwort-Dichte von 0,3 % bis 3 % (aber mindestens 3 Vorkommen).',
                'pl' => 'Gęstość głównej frazy kluczowej to liczba, ile razy główna fraza kluczowa pojawia się w tekście w stosunku do całkowitej długości tekstu na stronie. Zalecamy utrzymanie gęstości głównej frazy kluczowej w zakresie od 0,3% do 3% (ale co najmniej 3 razy).',
                'cs' => 'Hustota hlavní klíčové fráze je počet, kolikrát se hlavní klíčová fráze objeví ve vašem textu, ve srovnání s celkovým textem na této stránce. Doporučujeme udržovat hustotu hlavní klíčové fráze mezi 0,3 % a 3 % (ale alespoň 3x).',
                'pt' => 'A densidade da frase-chave principal é o número de vezes que sua frase-chave principal aparece no texto em comparação com o texto total da página. Recomendamos manter uma densidade da frase-chave principal entre 0,3% e 3% (mas pelo menos não menos de 3 vezes).',
                'nl' => 'De dichtheid van het focus trefwoord is het aantal keren dat uw focus trefwoord in uw tekst voorkomt in vergelijking met de totale tekst op die pagina. Wij raden een dichtheid van 0,3% tot 3% aan (maar minimaal 3 keer).',
                'ru' => 'Плотность ключевой фразы — это количество раз, которое ваша ключевая фраза появляется в тексте, по отношению к общему объему текста на этой странице. Мы рекомендуем поддерживать плотность ключевой фразы от 0,3 % до 3 % (но не менее 3 раз).',
            ],

            'keyphrase_density_individual' => [
                'en' => 'The individual words of your focus keyphrase should also appear on your content. "Individual words of focus keyphrase density" is the number of times these individual words appear in your text, compared to the total text of that page. We recommend that you maintain a density of at least 0.3%.',
                'fr' => 'Les mots individuels de votre phrase-clé principal doivent également apparaître sur votre contenu. «Densité de mots individuels de phrase-clé principal» est le nombre de fois que ces mots individuels apparaissent dans votre texte, par rapport au texte total de cette page. Nous vous recommandons de maintenir une densité d\'au moins 0,3%.',
                'es' => 'Las palabras individuales de su frase clave central también deben aparecer en su contenido. "Densidad de palabras individuales de frase clave central" es la cantidad de veces que estas palabras individuales aparecen en su texto, en comparación con el texto total de esa página. Recomendamos que mantenga una densidad de al menos 0.3%.',
                'it' => 'Parole chiave individuali della frase chiave principale dovrebbero anche apparire sui tuoi contenuti. "Densità di parole chiave individuali della frase chiave principale" è il numero di volte in cui queste singole parole appaiono nel tuo testo, rispetto al testo totale di quella pagina. Ti consigliamo di mantenere una densità di almeno lo 0.3%.',
                'de' => 'Die einzelnen Wörter Ihrer Fokus-Schlüsselphrase sollten ebenfalls in Ihrem Inhalt erscheinen. „Dichte der einzelnen Wörter der Fokus-Schlüsselphrase“ ist die Anzahl der Male, die diese einzelnen Wörter in Ihrem Text im Vergleich zum gesamten Text auf dieser Seite erscheinen. Wir empfehlen, eine Dichte von mindestens 0,3 % beizubehalten.',
                'pl' => 'Indywidualne słowa Twojej głównej frazy kluczowej powinny również pojawić się w treści. „Gęstość indywidualnych słów frazy kluczowej” to liczba razy, kiedy te indywidualne słowa pojawiają się w tekście w porównaniu do całkowitej ilości tekstu na stronie. Zalecamy utrzymanie gęstości co najmniej 0,3%.',
                'cs' => 'Jednotlivá slova vaší hlavní klíčové fráze by se měla objevit také v obsahu. "Denzita jednotlivých slov klíčové fráze" je počet, kolikrát se tato jednotlivá slova objeví ve vašem textu ve srovnání s celkovým textem na stránce. Doporučujeme udržovat denzitu alespoň 0,3%.',
                'pt' => 'As palavras individuais da sua frase-chave principal também devem aparecer no seu conteúdo. "Densidade de palavras individuais da frase-chave principal" é o número de vezes que essas palavras individuais aparecem no seu texto, em comparação com o texto total dessa página. Recomendamos que você mantenha uma densidade de pelo menos 0,3%.',
                'nl' => 'De individuele woorden van uw focus trefwoord zouden ook in uw inhoud moeten verschijnen. "Dichtheid van individuele woorden van het focus trefwoord" is het aantal keren dat deze individuele woorden in uw tekst verschijnen in vergelijking met de totale tekst op die pagina. Wij raden aan om een dichtheid van ten minste 0,3% te behouden.',
                'ru' => 'Отдельные слова вашей ключевой фразы также должны появляться в вашем контенте. "Плотность отдельных слов ключевой фразы" — это количество раз, которое эти отдельные слова встречаются в вашем тексте, по сравнению с общим текстом на странице. Мы рекомендуем поддерживать плотность не менее 0,3%.',
            ],
            'image_alt_attribute' => [
                'en' => 'If the image can not be shown to the visitor for any reason, you can add an image alt attribute (it is also called image caption or image description) to an image to display descriptive text in place. Search engines use alt text to decide what appears on the image. You should keep image alt text at around 125 characters and add your focus keyphrase at least 1 time into it.',
                'fr' => 'Si l\'image ne peut pas être montrée au visiteur pour une raison quelconque, vous pouvez ajouter un attribut alt d\'image (il est également appelé légende de l\'image ou description de l\'image) à une image pour afficher le texte descriptif en place. Les moteurs de recherche utilisent un texte alternatif pour décider de ce qui apparaît sur l\'image. Vous devez conserver le texte alternatif de l\'image à environ 125 caractères et y ajouter votre phrase-clé principal au moins 1 fois.',
                'es' => 'Si la imagen no se puede mostrar al visitante por algún motivo, puede agregar un atributo alt de imagen (también se denomina título de imagen o descripción de imagen) a una imagen para mostrar el texto descriptivo en su lugar. Los motores de búsqueda usan texto alternativo para decidir qué aparece en la imagen. Debe mantener el texto alternativo de la imagen en alrededor de 125 caracteres y agregar su frase clave central al menos 1 vez.',
                'it' => 'Se l\'immagine non può essere mostrata al visitatore per nessun motivo, è possibile aggiungere un attributo alt dell\'immagine (è anche chiamato didascalia o descrizione dell\'immagine) a un\'immagine per visualizzare il testo descrittivo in atto. I motori di ricerca usano il testo alternativo per decidere cosa appare sull\'immagine. Dovresti mantenere il testo alternativo dell\'immagine a circa 125 caratteri e aggiungere la frase chiave principale almeno 1 volta in esso.',
                'de' => 'Wenn das Bild aus irgendeinem Grund nicht dem Besucher angezeigt werden kann, können Sie ein Bild-alt-Attribut (auch als Bildunterschrift oder Bildbeschreibung bezeichnet) hinzufügen, um beschreibenden Text an seiner Stelle anzuzeigen. Suchmaschinen verwenden Alt-Text, um zu entscheiden, was auf dem Bild erscheint. Sie sollten den Alt-Text des Bildes auf etwa 125 Zeichen beschränken und Ihr Fokus-Schlüsselwort mindestens 1 Mal hinzufügen.',
                'pl' => 'Jeśli obrazek nie może zostać wyświetlony odwiedzającemu z jakiegokolwiek powodu, możesz dodać atrybut alt obrazu (nazywany również podpisem obrazu lub opisem obrazu), aby wyświetlić tekst opisowy w jego miejscu. Wyszukiwarki używają tekstu alternatywnego, aby określić, co pojawia się na obrazie. Należy utrzymać tekst alternatywny obrazu w granicach 125 znaków i dodać swoją główną frazę kluczową przynajmniej raz.',
                'cs' => 'Pokud obrázek z nějakého důvodu nelze zobrazit návštěvníkovi, můžete k obrázku přidat atribut alt (také nazývaný popisek obrázku nebo popis obrázku), který zobrazí popisný text na jeho místě. Vyhledávače používají alt text k rozhodnutí, co se na obrázku zobrazí. Měli byste udržovat alt text obrázku na přibližně 125 znacích a přidat svou hlavní klíčovou frázi alespoň 1krát.',
                'pt' => 'Se a imagem não puder ser exibida para o visitante por algum motivo, você pode adicionar um atributo alt à imagem (também chamado de legenda ou descrição da imagem) para exibir o texto descritivo no lugar. Os motores de busca usam o texto alt para decidir o que aparece na imagem. Você deve manter o texto alt da imagem em cerca de 125 caracteres e adicionar sua frase-chave principal pelo menos 1 vez.',
                'nl' => 'Als de afbeelding om welke reden dan ook niet aan de bezoeker kan worden getoond, kunt u een alt-attribuut voor de afbeelding toevoegen (dit wordt ook wel afbeeldingstitel of afbeeldingsomschrijving genoemd) om beschrijvende tekst in de plaats daarvan weer te geven. Zoekmachines gebruiken alt-tekst om te bepalen wat er op de afbeelding verschijnt. U moet de alt-tekst van de afbeelding rond de 125 tekens houden en uw focus trefwoord ten minste 1 keer erin toevoegen.',
                'ru' => 'Если изображение по какой-то причине не может быть показано посетителю, вы можете добавить атрибут alt для изображения (он также называется заголовком изображения или описанием изображения), чтобы отобразить описательный текст на его месте. Поисковые системы используют alt-текст, чтобы определить, что отображается на изображении. Вам следует ограничить alt-текст изображения до 125 символов и добавить вашу ключевую фразу хотя бы 1 раз.',
            ],

            'text_length' => [
                'en' => 'To be able to rank, every page on your site must contain a certain amount of words. The minimum length of the texts (including short description, description, content of a page, etc.) will vary depending on the type of page. For example: category page needs at least 100 words; regular content page needs at least 300 words; etc.',
                'fr' => 'Pour pouvoir se classer, chaque page de votre site doit contenir un certain nombre de mots. La longueur minimale des textes (y compris une brève description, une description, le contenu d\'une page, etc.) variera en fonction du type de page. Par exemple: la page de catégorie a besoin d\'au moins 100 mots; la page de contenu standard nécessite au moins 300 mots; etc.',
                'es' => 'Para poder clasificar, cada página de su sitio debe contener una cierta cantidad de palabras. La longitud mínima de los textos (incluida una breve descripción, descripción, contenido de una página, etc.) variará según el tipo de página. Por ejemplo: la página de categoría necesita al menos 100 palabras; la página de contenido normal necesita al menos 300 palabras; etc.',
                'it' => 'Per essere in grado di classificare, ogni pagina del tuo sito deve contenere un certo numero di parole. La lunghezza minima dei testi (compresa una breve descrizione, descrizione, contenuto di una pagina, ecc.) Varierà a seconda del tipo di pagina. Ad esempio: la pagina della categoria richiede almeno 100 parole; la pagina dei contenuti regolari richiede almeno 300 parole; eccetera.',
                'de' => 'Um ein Ranking zu erreichen, muss jede Seite auf Ihrer Website eine bestimmte Anzahl von Wörtern enthalten. Die Mindestlänge der Texte (einschließlich kurzer Beschreibung, Beschreibung, Inhalt einer Seite usw.) variiert je nach Art der Seite. Zum Beispiel: Eine Kategorieseite benötigt mindestens 100 Wörter; eine reguläre Inhaltsseite benötigt mindestens 300 Wörter; usw.',
                'pl' => 'Aby strona mogła zostać sklasyfikowana, każda strona na Twojej witrynie musi zawierać określoną liczbę słów. Minimalna długość tekstów (w tym krótki opis, opis, zawartość strony itp.) będzie się różnić w zależności od rodzaju strony. Na przykład: strona kategorii wymaga co najmniej 100 słów; strona z treścią standardową wymaga co najmniej 300 słów; itd.',
                'cs' => 'Pro možnost zařazení do hodnocení musí každá stránka na vašem webu obsahovat určité množství slov. Minimální délka textů (včetně krátkého popisu, popisu, obsahu stránky atd.) se bude lišit v závislosti na typu stránky. Například: stránka kategorie potřebuje alespoň 100 slov; běžná stránka s obsahem potřebuje alespoň 300 slov; atd.',
                'pt' => 'Para poder classificar, cada página do seu site deve conter uma certa quantidade de palavras. O comprimento mínimo dos textos (incluindo descrição curta, descrição, conteúdo da página, etc.) variará dependendo do tipo de página. Por exemplo: a página da categoria precisa de pelo menos 100 palavras; a página de conteúdo regular precisa de pelo menos 300 palavras; etc.',
                'nl' => 'Om te kunnen ranken, moet elke pagina op uw site een bepaald aantal woorden bevatten. De minimale lengte van de teksten (inclusief korte beschrijving, beschrijving, inhoud van een pagina, enz.) varieert afhankelijk van het type pagina. Bijvoorbeeld: een categoriepagina heeft minstens 100 woorden nodig; een reguliere inhoudspagina heeft minstens 300 woorden nodig; enz.',
                'ru' => 'Чтобы страница могла быть проиндексирована, она должна содержать определенное количество слов. Минимальная длина текста (включая краткое описание, описание, содержание страницы и т. д.) будет зависеть от типа страницы. Например: страница категории должна содержать не менее 100 слов; обычная страница контента должна содержать не менее 300 слов; и т. д.',
            ],
            'meta_description_length' => [
                'en' => 'The meta description is a short text that can be applied to your page or site, summarizing what the page is about and attracting people to visit your website. Meta description should be between 120 characters and 156 characters.',
                'fr' => 'La méta description est un court texte qui peut être appliqué à votre page ou site, résumant le sujet de la page et incitant les gens à visiter votre site Web. La méta description doit comprendre entre 120 et 156 caractères.',
                'es' => 'La meta descripción es un texto breve que puede aplicarse a su página o sitio, que resume de qué trata la página y atrae a las personas a visitar su sitio web. La meta descripción debe tener entre 120 y 156 caracteres.',
                'it' => 'La meta descrizione è un breve testo che può essere applicato alla tua pagina o al tuo sito, riassumendo il contenuto della pagina e attirando le persone a visitare il tuo sito web. La meta descrizione dovrebbe essere compresa tra 120 e 156 caratteri.',
                'de' => 'Die Meta-Beschreibung ist ein kurzer Text, der auf Ihre Seite oder Website angewendet werden kann und zusammenfasst, worum es auf der Seite geht und Menschen dazu anregt, Ihre Website zu besuchen. Die Meta-Beschreibung sollte zwischen 120 und 156 Zeichen lang sein.',
                'pl' => 'Meta opis to krótki tekst, który można zastosować do strony lub witryny, podsumowując, o czym jest strona i zachęcając ludzi do odwiedzenia witryny. Meta opis powinien mieć od 120 do 156 znaków.',
                'cs' => 'Meta popis je krátký text, který může být aplikován na vaši stránku nebo web, shrnující, o čem stránka je, a lákající lidi na vaši webovou stránku. Meta popis by měl mít mezi 120 a 156 znaky.',
                'pt' => 'A meta descrição é um texto curto que pode ser aplicado à sua página ou site, resumindo o conteúdo da página e atraindo pessoas para visitar seu site. A meta descrição deve ter entre 120 e 156 caracteres.',
                'nl' => 'De meta-beschrijving is een korte tekst die kan worden toegepast op uw pagina of site, die samenvat waar de pagina over gaat en mensen aanmoedigt uw website te bezoeken. De meta-beschrijving moet tussen de 120 en 156 tekens bevatten.',
                'ru' => 'Мета-описание — это короткий текст, который может быть применен к вашей странице или сайту, резюмируя, о чем страница, и привлекая людей посетить ваш сайт. Мета-описание должно быть длиной от 120 до 156 символов.',
            ],

            'seo_title_width' => [
                'en' => 'Meta title will be displayed as the title of your snippet to people on the results pages of the search engines and may differ from the title of your page. We recommend the length of the meta title should be less than 60 characters.',
                'fr' => 'Le titre de méta sera affiché comme le titre de votre extrait aux personnes sur les pages de résultats des moteurs de recherche et peut différer du titre de votre page. Nous recommandons que la longueur du méta-titre soit inférieure à 60 caractères.',
                'es' => 'El meta título se mostrará como el título de su fragmento a las personas en las páginas de resultados de los motores de búsqueda y puede diferir del título de su página. Recomendamos que la longitud del meta título sea inferior a 60 caracteres.',
                'it' => 'Il meta titolo verrà visualizzato come titolo dello snippet per le persone sulle pagine dei risultati dei motori di ricerca e potrebbe differire dal titolo della pagina. Raccomandiamo che la lunghezza del meta titolo dovrebbe essere inferiore a 60 caratteri.',
                'de' => 'Der Meta-Titel wird als Titel Ihres Snippets für Personen auf den Ergebnisseiten der Suchmaschinen angezeigt und kann vom Titel Ihrer Seite abweichen. Wir empfehlen, dass die Länge des Meta-Titels weniger als 60 Zeichen beträgt.',
                'pl' => 'Tytuł meta będzie wyświetlany jako tytuł twojego fragmentu dla osób na stronach wyników wyszukiwania i może różnić się od tytułu twojej strony. Zalecamy, aby długość tytułu meta była krótsza niż 60 znaków.',
                'cs' => 'Meta titulek bude zobrazen jako titulka vašeho výpisu pro lidi na stránkách výsledků vyhledávačů a může se lišit od názvu vaší stránky. Doporučujeme, aby délka meta titulky byla menší než 60 znaků.',
                'pt' => 'O título meta será exibido como o título do seu fragmento para as pessoas nas páginas de resultados dos motores de busca e pode ser diferente do título da sua página. Recomendamos que o comprimento do título meta seja inferior a 60 caracteres.',
                'nl' => 'De meta-titel wordt weergegeven als de titel van je snippet voor mensen op de resultatenpagina’s van zoekmachines en kan verschillen van de titel van je pagina. We raden aan dat de lengte van de meta-titel minder dan 60 tekens is.',
                'ru' => 'Мета-заголовок будет отображаться как заголовок вашего фрагмента для людей на страницах результатов поисковых систем и может отличаться от заголовка вашей страницы. Мы рекомендуем, чтобы длина мета-заголовка была менее 60 символов.',
            ],
            'keyphrase_in_meta_desc' => [
                'en' => 'The meta description is a short piece of text that provides search engines and visitors a sort of summary of what your page is about. You should let all words from the focus keyphrase appear 1 or 2 times in meta description.',
                'fr' => 'La méta description est un court texte qui fournit aux moteurs de recherche et aux visiteurs une sorte de résumé de l\'objet de votre page. Vous devez laisser tous les mots de la phrase-clé principal apparaître 1 ou 2 fois dans la méta description.',
                'es' => 'La meta descripción es un breve texto que proporciona a los motores de búsqueda y visitantes una especie de resumen de lo que trata su página. Debe dejar que todas las palabras de la frase clave central aparezcan 1 o 2 veces en la meta descripción.',
                'it' => 'La meta descrizione è un breve pezzo di testo che fornisce ai motori di ricerca e ai visitatori una sorta di sommario di cosa tratta la tua pagina. Dovresti far apparire tutte le parole dalla frase chiave principale 1 o 2 volte nella meta descrizione.',
                'de' => 'Die Meta-Beschreibung ist ein kurzer Text, der den Suchmaschinen und Besuchern eine Art Zusammenfassung dessen gibt, worum es auf Ihrer Seite geht. Alle Wörter aus dem Fokus-Keyword sollten 1 oder 2 Mal in der Meta-Beschreibung erscheinen.',
                'pl' => 'Meta opis to krótki tekst, który zapewnia wyszukiwarkom i odwiedzającym rodzaj podsumowania, o czym jest twoja strona. Powinieneś umożliwić wystąpienie wszystkich słów z frazy kluczowej 1 lub 2 razy w meta opisie.',
                'cs' => 'Meta popis je krátký text, který poskytuje vyhledávačům a návštěvníkům jakýsi souhrn o čem je vaše stránka. Měli byste nechat všechna slova z klíčové fráze objevit se 1 nebo 2krát v meta popisu.',
                'pt' => 'A meta descrição é um curto trecho de texto que fornece aos motores de busca e visitantes um tipo de resumo sobre o que sua página trata. Você deve permitir que todas as palavras da frase-chave apareçam 1 ou 2 vezes na meta descrição.',
                'nl' => 'De meta-beschrijving is een kort stuk tekst dat zoekmachines en bezoekers een soort samenvatting geeft van waar je pagina over gaat. Je zou alle woorden uit de focus-zoekterm 1 of 2 keer in de meta-beschrijving moeten laten verschijnen.',
                'ru' => 'Мета-описание — это короткий текст, который предоставляет поисковым системам и посетителям своего рода резюме того, о чем ваша страница. Вы должны позволить всем словам из основной ключевой фразы появляться 1 или 2 раза в мета-описании.',
            ],

            'keyphrase_in_slug' => [
                'en' => 'A slug is the part of a URL that identifies a specific page on a website in an easy-to-read form. For short focus keyphrases (1-2 words length), you should use all keyphrase words in URL. For longer focus keyphrases (>2 words length), you should use more than half keyphrase words in URL.',
                'fr' => 'Un slug est la partie d\'une URL qui identifie une page spécifique sur un site Web sous une forme facile à lire. Pour les phrase-clé principal court (longueur de 1 à 2 mots), vous devez utiliser tous les mots clés dans l\'URL. Pour les phrase-clé principal plus longues (> 2 mots de longueur), vous devez utiliser plus de la moitié des mots clés dans l\'URL.',
                'es' => 'Una slug es la parte de una URL que identifica una página específica en un sitio web en un formulario fácil de leer. Para frases clave central corto (1-2 palabras de longitud), debe usar todas las palabras clave en URL. Para frases clave central más largas (> 2 palabras de longitud), debe usar más de la mitad de las palabras clave en la URL.',
                'it' => 'Una slug è la parte di un URL che identifica una pagina specifica su un sito Web in un formato di facile lettura. Per frasi chiave principale breve (lunghezza di 1-2 parole), dovresti usare tutte le parole chiave nell\'URL. Per frasi chiave principale più lungo (lunghezza > 2 parole), dovresti usare più della metà delle parole chiave nell\'URL.',
                'de' => 'Ein Slug ist der Teil einer URL, der eine bestimmte Seite auf einer Website in einer leicht lesbaren Form identifiziert. Für kurze Schlüsselphrasen (1-2 Wörter lang) sollten Sie alle Schlüsselwortwörter in der URL verwenden. Für längere Schlüsselphrasen (>2 Wörter) sollten Sie mehr als die Hälfte der Schlüsselwörter in der URL verwenden.',
                'pl' => 'Slug to część adresu URL, która identyfikuje konkretną stronę na stronie internetowej w łatwej do odczytania formie. Dla krótkich fraz kluczowych (1-2 słowa) należy używać wszystkich słów kluczowych w URL. Dla dłuższych fraz kluczowych (>2 słowa) należy używać więcej niż połowy słów kluczowych w URL.',
                'cs' => 'Slug je část URL, která identifikuje konkrétní stránku na webu ve snadno čitelném formátu. Pro krátké klíčové fráze (1-2 slova) byste měli použít všechna klíčová slova v URL. Pro delší klíčové fráze (>2 slova) byste měli použít více než polovinu klíčových slov v URL.',
                'pt' => 'Um slug é a parte de uma URL que identifica uma página específica em um site de forma fácil de ler. Para frases-chave curtas (1-2 palavras), você deve usar todas as palavras-chave na URL. Para frases-chave mais longas (>2 palavras), você deve usar mais da metade das palavras-chave na URL.',
                'nl' => 'Een slug is het deel van een URL dat een specifieke pagina op een website op een gemakkelijk leesbare manier identificeert. Voor korte zoekwoorden (1-2 woorden lang) moet je alle zoekwoordwoorden in de URL gebruiken. Voor langere zoekwoorden (>2 woorden) moet je meer dan de helft van de zoekwoorden in de URL gebruiken.',
                'ru' => 'Слаг — это часть URL, которая идентифицирует конкретную страницу на сайте в легкочитаемой форме. Для коротких ключевых фраз (1-2 слова) следует использовать все ключевые слова в URL. Для более длинных ключевых фраз (>2 слова) следует использовать более половины ключевых слов в URL.'
            ],
            'minor_keyphrase_in_content' => [
                'en' => 'Related keyphrase is a phrase closely related to the focus keyphrase, usually long, descriptive and may not have a high level of traffic. You should allow each related keyphrases to appear at least 1 time in your content.',
                'fr' => 'La phrase-clé associée est une phrase étroitement liée à la phrase-clé principal, généralement longue, descriptive et peut ne pas avoir un niveau de trafic élevé. Vous devez autoriser chaque phrase-clé associée à apparaître au moins 1 fois dans votre contenu.',
                'es' => 'La frase clave relacionada es una frase estrechamente relacionada con la frase clave central, generalmente larga, descriptiva y puede no tener un alto nivel de tráfico. Debe permitir que cada frase clave relacionada aparezca al menos 1 vez en su contenido.',
                'it' => 'La frase chiave correlata è una frase strettamente correlata alla frase chiave principale, generalmente lunga, descrittiva e potrebbe non avere un livello elevato di traffico. Dovresti consentire ad ogni frase chiave correlata di apparire almeno 1 volta nel tuo contenuto.',
                'de' => 'Verwandte Schlüsselwörter sind Phrasen, die eng mit dem Fokus-Schlüsselwort verbunden sind, in der Regel lang, beschreibend und möglicherweise nicht sehr traffiquiert. Sie sollten es jedem verwandten Schlüsselwort ermöglichen, mindestens einmal in Ihrem Inhalt zu erscheinen.',
                'pl' => 'Powiązane frazy kluczowe to wyrażenia ściśle związane z główną frazą kluczową, zazwyczaj długie, opisowe i mogące nie generować dużego ruchu. Powinieneś umożliwić każdej powiązanej frazie kluczowej pojawienie się co najmniej raz w treści.',
                'cs' => 'Související klíčová fráze je fráze, která je úzce spjata s hlavní klíčovou frází, obvykle dlouhá, popisná a nemusí mít vysokou úroveň návštěvnosti. Měli byste umožnit, aby se každá související klíčová fráze objevila alespoň 1krát ve vašem obsahu.',
                'pt' => 'A frase-chave relacionada é uma frase intimamente relacionada à frase-chave principal, geralmente longa, descritiva e pode não ter um alto nível de tráfego. Você deve permitir que cada frase-chave relacionada apareça pelo menos 1 vez no seu conteúdo.',
                'nl' => 'Gerelateerde zoekwoorden zijn zinnen die nauw verwant zijn aan de focus zoekterm, meestal lang, beschrijvend en mogelijk niet veel verkeer genereren. U moet elke gerelateerde zoekterm toestaan om ten minste 1 keer in uw inhoud te verschijnen.',
                'ru' => 'Связанная ключевая фраза — это фраза, тесно связанная с основной ключевой фразой, обычно длинная, описательная и может не иметь большого потока трафика. Вы должны позволить каждой связанной ключевой фразе появиться хотя бы 1 раз в вашем контенте.'
            ],
            'minor_keyphrase_in_content_individual' => [
                'en' => 'The individual words of your related keyphrase should also appear on your content. "Individual words of related keyphrase density" is the number of times these individual words appear in your text, compared to the total text of that page. We recommend that you maintain a density of at least 0.3%.',
                'fr' => 'Les mots individuels de votre phrase-clé associée doivent également apparaître sur votre contenu. "Densité de mots individuels de phrase-clé associée" est le nombre de fois où ces mots individuels apparaissent dans votre texte, par rapport au texte total de cette page. Nous vous recommandons de maintenir une densité d\'au moins 0,3%.',
                'es' => 'Las palabras individuales de su frase clave relacionada también deben aparecer en su contenido. "Densidad de palabras individuales de frase clave relacionada" es el número de veces que estas palabras individuales aparecen en su texto, en comparación con el texto total de esa página. Recomendamos que mantenga una densidad de al menos 0.3%.',
                'it' => 'Parole chiave individuali della frase chiave correlata dovrebbero apparire anche nei tuoi contenuti. "Densità di parole chiave individuali della frase chiave correlata" è il numero di volte in cui queste singole parole appaiono nel tuo testo, rispetto al testo totale di quella pagina. Ti consigliamo di mantenere una densità di almeno lo 0.3%.',
                'de' => 'Die einzelnen Wörter Ihrer verwandten Schlüsselphrase sollten ebenfalls in Ihrem Inhalt erscheinen. "Dichte einzelner Wörter der verwandten Schlüsselphrase" ist die Anzahl der Male, die diese einzelnen Wörter in Ihrem Text erscheinen, im Vergleich zum gesamten Text auf dieser Seite. Wir empfehlen, eine Dichte von mindestens 0,3% beizubehalten.',
                'pl' => 'Poszczególne słowa Twojej powiązanej frazy kluczowej powinny również pojawiać się w treści. "Gęstość pojedynczych słów frazy kluczowej" to liczba wystąpień tych pojedynczych słów w tekście w porównaniu do całkowitej długości tekstu na danej stronie. Zalecamy utrzymanie gęstości na poziomie co najmniej 0,3%.',
                'cs' => 'Jednotlivá slova vaší související klíčové fráze by se měla také objevit ve vašem obsahu. "Denzita jednotlivých slov související klíčové fráze" je počet výskytů těchto jednotlivých slov ve vašem textu ve srovnání s celkovým textem na této stránce. Doporučujeme udržovat denzitu alespoň 0,3%.',
                'pt' => 'As palavras individuais da sua frase-chave relacionada também devem aparecer no seu conteúdo. "Densidade de palavras individuais da frase-chave relacionada" é o número de vezes que essas palavras individuais aparecem no seu texto, em comparação com o texto total dessa página. Recomendamos que mantenha uma densidade de pelo menos 0,3%.',
                'nl' => 'De individuele woorden van je gerelateerde zoekterm zouden ook in je content moeten verschijnen. "Dichtheid van individuele woorden van gerelateerde zoektermen" is het aantal keren dat deze individuele woorden in je tekst verschijnen, vergeleken met de totale tekst op die pagina. We raden aan een dichtheid van minstens 0,3% te handhaven.',
                'ru' => 'Индивидуальные слова вашей связанной ключевой фразы также должны появляться в вашем контенте. "Плотность индивидуальных слов связанной ключевой фразы" — это количество раз, когда эти отдельные слова появляются в вашем тексте, по сравнению с общим текстом на этой странице. Мы рекомендуем поддерживать плотность не менее 0,3%.'
            ],

            'minor_keyphrase_in_title' => [
                'en' => 'Related keyphrase is a phrase closely related to the focus keyphrase, usually long, descriptive and may not have a high level of traffic. You should allow related keyphrases to appear at least 1 time in your [page_title] or meta title',
                'fr' => 'La phrase-clé associée est une phrase étroitement liée à la phrase-clé principal, généralement longue, descriptive et peut ne pas avoir un niveau de trafic élevé. Vous devez autoriser les phrases-clés associées à apparaître au moins 1 fois dans votre [page_title] ou titre méta.',
                'es' => 'La frase clave relacionada es una frase estrechamente relacionada con la frase clave central, generalmente larga, descriptiva y puede no tener un alto nivel de tráfico. Debes permitir que las frases clave relacionadas aparezcan al menos 1 vez en tu [page_title] o meta título.',
                'it' => 'La frase chiave correlata è una frase strettamente correlata alla frase chiave principale, generalmente lunga, descrittiva e potrebbe non avere un livello elevato di traffico. Dovresti consentire ad ogni frase chiave correlata di apparire almeno 1 volta nel tuo [page_title] or meta titolo',
                'de' => 'Der verwandte Schlüsselbegriff ist ein Ausdruck, der eng mit dem Haupt-Schlüsselbegriff verbunden ist, normalerweise lang, beschreibend und möglicherweise nicht mit hohem Traffic verbunden. Sie sollten verwandte Schlüsselbegriffe mindestens einmal in Ihrem [page_title] oder Meta-Titel zulassen.',
                'pl' => 'Związane frazy kluczowe to wyrażenia ściśle związane z główną frazą kluczową, zazwyczaj długie, opisowe i mogące nie generować dużego ruchu. Powinieneś pozwolić na pojawienie się powiązanych fraz kluczowych przynajmniej 1 raz w tytule strony lub tytule meta.',
                'cs' => 'Související klíčová fráze je fráze úzce související s hlavní klíčovou frází, obvykle dlouhá, popisná a může mít nízkou úroveň návštěvnosti. Měli byste umožnit, aby související klíčové fráze byly zobrazeny alespoň 1krát ve vašem [page_title] nebo meta titulu.',
                'pt' => 'A frase chave relacionada é uma frase estreitamente relacionada à frase chave principal, geralmente longa, descritiva e pode não ter um alto nível de tráfego. Você deve permitir que frases chave relacionadas apareçam pelo menos 1 vez em seu [page_title] ou título meta.',
                'nl' => 'Een gerelateerde zoekterm is een frase die nauw verwant is aan de focuszoekterm, meestal lang, beschrijvend en mogelijk niet veel verkeer heeft. U moet gerelateerde zoektermen ten minste 1 keer laten verschijnen in uw [page_title] of meta titel.',
                'ru' => 'Связанная ключевая фраза — это фраза, тесно связанная с основной ключевой фразой, обычно длинная, описательная и может не иметь высокого уровня трафика. Вы должны разрешить появление связанных ключевых фраз хотя бы один раз в вашем [page_title] или мета-титуле.',
            ],

            'minor_keyphrase_in_page_title' => [
                'en' => 'Related keyphrase is a phrase closely related to the focus keyphrase, usually long, descriptive and may not have a high level of traffic. You should allow related keyphrases to appear at least 1 time in your [page_title].',
                'fr' => 'La phrase-clé associée est une phrase étroitement liée à la phrase-clé principal, généralement longue, descriptive et peut ne pas avoir un niveau de trafic élevé. Vous devez autoriser les phrases-clés associées à apparaître au moins 1 fois dans votre [page_title].',
                'es' => 'La frase clave relacionada es una frase estrechamente relacionada con la frase clave central, generalmente larga, descriptiva y puede no tener un alto nivel de tráfico. Debe permitir que las frases clave relacionadas aparezcan al menos 1 vez en su [page_title].',
                'it' => 'La frase chiave correlata è una frase strettamente correlata alla frase chiave principale, generalmente lunga, descrittiva e potrebbe non avere un livello elevato di traffico. Dovresti consentire ad ogni frase chiave correlata di apparire almeno 1 volta nel tuo [page_title].',
                'de' => 'Der verwandte Schlüsselbegriff ist ein Ausdruck, der eng mit dem Haupt-Schlüsselbegriff verbunden ist, normalerweise lang, beschreibend und möglicherweise nicht mit hohem Traffic verbunden. Sie sollten verwandte Schlüsselbegriffe mindestens einmal in Ihrem [page_title] erscheinen lassen.',
                'pl' => 'Związane frazy kluczowe to wyrażenia ściśle związane z główną frazą kluczową, zazwyczaj długie, opisowe i mogące nie generować dużego ruchu. Powinieneś pozwolić na pojawienie się powiązanych fraz kluczowych przynajmniej 1 raz w tytule strony.',
                'cs' => 'Související klíčová fráze je fráze úzce související s hlavní klíčovou frází, obvykle dlouhá, popisná a může mít nízkou úroveň návštěvnosti. Měli byste umožnit, aby související klíčové fráze byly zobrazeny alespoň 1krát ve vašem [page_title].',
                'pt' => 'A frase chave relacionada é uma frase estreitamente relacionada à frase chave principal, geralmente longa, descritiva e pode não ter um alto nível de tráfego. Você deve permitir que frases chave relacionadas apareçam pelo menos 1 vez em seu [page_title].',
                'nl' => 'Een gerelateerde zoekterm is een frase die nauw verwant is aan de focuszoekterm, meestal lang, beschrijvend en mogelijk niet veel verkeer heeft. U moet gerelateerde zoektermen ten minste 1 keer laten verschijnen in uw [page_title].',
                'ru' => 'Связанная ключевая фраза — это фраза, тесно связанная с основной ключевой фразой, обычно длинная, описательная и может не иметь высокого уровня трафика. Вы должны разрешить появление связанных ключевых фраз хотя бы один раз в вашем [page_title].',
            ],

            'minor_keyphrase_in_desc' => [
                'en' => 'Related keyphrase is a phrase closely related to the focus keyphrase, usually long, descriptive and may not have a high level of traffic. You should allow related keyphrases to appear at least 1 time in your meta description',
                'fr' => 'La phrase-clé associée est une phrase étroitement liée à la phrase-clé principal, généralement longue, descriptive et peut ne pas avoir un niveau de trafic élevé. Vous devez autoriser les phrases-clés associées à apparaître au moins 1 fois dans votre méta description',
                'es' => 'La frase clave relacionada es una frase estrechamente relacionada con la frase clave central, generalmente larga, descriptiva y puede no tener un alto nivel de tráfico. Debe permitir que las frases clave relacionadas aparezcan al menos 1 vez en su meta descripción.',
                'it' => 'La frase chiave correlata è una frase strettamente correlata alla frase chiave principale, generalmente lunga, descrittiva e potrebbe non avere un livello elevato di traffico. Dovresti consentire ad ogni frase chiave correlata di apparire almeno 1 volta nel tuo meta descrizione',
                'de' => 'Der verwandte Schlüsselbegriff ist ein Ausdruck, der eng mit dem Haupt-Schlüsselbegriff verbunden ist, normalerweise lang, beschreibend und möglicherweise nicht mit hohem Traffic verbunden. Sie sollten verwandte Schlüsselbegriffe mindestens einmal in Ihrer Meta-Beschreibung zulassen.',
                'pl' => 'Związane frazy kluczowe to wyrażenia ściśle związane z główną frazą kluczową, zazwyczaj długie, opisowe i mogące nie generować dużego ruchu. Powinieneś pozwolić na pojawienie się powiązanych fraz kluczowych przynajmniej 1 raz w opisie meta.',
                'cs' => 'Související klíčová fráze je fráze úzce související s hlavní klíčovou frází, obvykle dlouhá, popisná a může mít nízkou úroveň návštěvnosti. Měli byste umožnit, aby související klíčové fráze byly zobrazeny alespoň 1krát v meta popisu.',
                'pt' => 'A frase chave relacionada é uma frase estreitamente relacionada à frase chave principal, geralmente longa, descritiva e pode não ter um alto nível de tráfego. Você deve permitir que frases chave relacionadas apareçam pelo menos 1 vez em sua meta descrição.',
                'nl' => 'Een gerelateerde zoekterm is een frase die nauw verwant is aan de focuszoekterm, meestal lang, beschrijvend en mogelijk niet veel verkeer heeft. U moet gerelateerde zoektermen ten minste 1 keer laten verschijnen in uw meta-beschrijving.',
                'ru' => 'Связанная ключевая фраза — это фраза, тесно связанная с основной ключевой фразой, обычно длинная, описательная и может не иметь высокого уровня трафика. Вы должны разрешить появление связанных ключевых фраз хотя бы один раз в вашем мета-описании.',
            ],

            'minor_keyphrase_acceptance' => [
                'en' => 'Related keyphrase is a phrase closely related to the focus keyphrase, usually long, descriptive and may not have a high level of traffic. You should allow related keyphrases to appear at least 1 time in your title or meta title',
                'fr' => 'La phrase-clé associée est une phrase étroitement liée à la phrase-clé principal, généralement longue, descriptive et peut ne pas avoir un niveau de trafic élevé. Vous devez autoriser les phrases-clés associées à apparaître au moins 1 fois dans votre titre ou méta-titre.',
                'es' => 'La frase clave relacionada es una frase estrechamente relacionada con la frase clave central, generalmente larga, descriptiva y puede no tener un alto nivel de tráfico. Debe permitir que aparezcan frases clave relacionadas al menos 1 vez en su título o metatítulo.',
                'it' => 'La frase chiave correlata è una frase strettamente correlata alla frase chiave principale, generalmente lunga, descrittiva e potrebbe non avere un livello elevato di traffico. Dovresti consentire ad ogni frase chiave correlata di apparire almeno 1 volta nel tuo titolo o meta titolo',
                'de' => 'Der verwandte Schlüsselbegriff ist ein Ausdruck, der eng mit dem Haupt-Schlüsselbegriff verbunden ist, normalerweise lang, beschreibend und möglicherweise nicht mit hohem Traffic verbunden. Sie sollten verwandte Schlüsselbegriffe mindestens einmal in Ihrem Titel oder Meta-Titel erscheinen lassen.',
                'pl' => 'Związane frazy kluczowe to wyrażenia ściśle związane z główną frazą kluczową, zazwyczaj długie, opisowe i mogące nie generować dużego ruchu. Powinieneś pozwolić na pojawienie się powiązanych fraz kluczowych przynajmniej 1 raz w tytule lub tytule meta.',
                'cs' => 'Související klíčová fráze je fráze úzce související s hlavní klíčovou frází, obvykle dlouhá, popisná a může mít nízkou úroveň návštěvnosti. Měli byste umožnit, aby související klíčové fráze byly zobrazeny alespoň 1krát v názvu nebo meta titulu.',
                'pt' => 'A frase chave relacionada é uma frase estreitamente relacionada à frase chave principal, geralmente longa, descritiva e pode não ter um alto nível de tráfego. Você deve permitir que frases chave relacionadas apareçam pelo menos 1 vez em seu título ou título meta.',
                'nl' => 'Een gerelateerde zoekterm is een frase die nauw verwant is aan de focuszoekterm, meestal lang, beschrijvend en mogelijk niet veel verkeer heeft. U moet gerelateerde zoektermen ten minste 1 keer laten verschijnen in uw titel of meta titel.',
                'ru' => 'Связанная ключевая фраза — это фраза, тесно связанная с основной ключевой фразой, обычно длинная, описательная и может не иметь высокого уровня трафика. Вы должны разрешить появление связанных ключевых фраз хотя бы один раз в вашем заголовке или мета-титуле.',
            ],

            /* Readability */
            'not_enough_content' => [
                'en' => 'Please ensure that your content has more than 50 characters. A quality content will attract audiences and offer a high rank in the search engines, so write some more text!',
                'fr' => 'Veuillez vous assurer que votre contenu comporte plus de 50 caractères. Un contenu de qualité attirera le public et offrira un rang élevé dans les moteurs de recherche, alors écrivez un peu plus de texte !',
                'es' => 'Asegúrese de que su contenido tenga más de 50 caracteres. Un contenido de calidad atraerá audiencias y ofrecerá un alto rango en los motores de búsqueda, ¡así que escriba más texto!',
                'it' => 'Assicurati che i tuoi contenuti contengano più di 50 caratteri. Un contenuto di qualità attirerà il pubblico e offrirà un alto rango nei motori di ricerca, quindi scrivi altro testo!',
                'de' => 'Stellen Sie sicher, dass Ihr Inhalt mehr als 50 Zeichen enthält. Qualitativ hochwertiger Inhalt wird ein Publikum anziehen und ein hohes Ranking in den Suchmaschinen bieten. Schreiben Sie also noch mehr Text!',
                'pl' => 'Upewnij się, że Twoja treść ma więcej niż 50 znaków. Jakościowa treść przyciągnie publiczność i zapewni wysoką pozycję w wyszukiwarkach, więc napisz trochę więcej tekstu!',
                'cs' => 'Ujistěte se, že váš obsah má více než 50 znaků. Kvalitní obsah přitahuje publikum a nabídne vysokou pozici ve vyhledávačích, takže napište ještě nějaký text!',
                'pt' => 'Certifique-se de que seu conteúdo tenha mais de 50 caracteres. Um conteúdo de qualidade atrairá o público e oferecerá uma classificação elevada nos motores de busca, então escreva mais texto!',
                'nl' => 'Zorg ervoor dat je content meer dan 50 tekens bevat. Kwalitatieve inhoud zal het publiek aantrekken en een hoge ranking in zoekmachines opleveren, dus schrijf wat meer tekst!',
                'ru' => 'Пожалуйста, убедитесь, что ваш контент содержит более 50 символов. Качественный контент привлечет аудиторию и обеспечит высокий рейтинг в поисковых системах, поэтому напишите немного больше текста!',
            ],

            'sentence_length' => [
                'en' => 'A text consisting of many long sentences is difficult to read because it is more difficult to process long sentences. If more than 25% of your sentences are more than 20 words, some of these long sentences should be shortened.',
                'fr' => 'Un texte composé de nombreuses phrases longues est difficile à lire car il est plus difficile de traiter de longues phrases. Si plus de 25% de vos phrases sont plus de 20 mots, certaines de ces longues phrases doivent être raccourcies.',
                'es' => 'Un texto que consta de muchas oraciones largas es difícil de leer porque es más difícil procesar oraciones largas. Si más del 25% de sus oraciones son más de 20 palabras, algunas de estas oraciones largas deben acortarse.',
                'it' => 'Un testo composto da molte frasi lunghe è difficile da leggere perché è più difficile elaborare frasi lunghe. Se più del 25% delle tue frasi sono più di 20 parole, alcune di queste frasi lunghe dovrebbero essere abbreviate.',
                'de' => 'Ein Text, der aus vielen langen Sätzen besteht, ist schwer zu lesen, da es schwieriger ist, lange Sätze zu verarbeiten. Wenn mehr als 25% Ihrer Sätze mehr als 20 Wörter enthalten, sollten einige dieser langen Sätze gekürzt werden.',
                'pl' => 'Tekst składający się z wielu długich zdań jest trudny do przeczytania, ponieważ trudniej jest przetwarzać długie zdania. Jeśli więcej niż 25% twoich zdań ma więcej niż 20 słów, niektóre z tych długich zdań powinny zostać skrócone.',
                'cs' => 'Text složený z mnoha dlouhých vět je těžké číst, protože je obtížnější zpracovávat dlouhé věty. Pokud více než 25 % vašich vět má více než 20 slov, některé z těchto dlouhých vět by měly být zkráceny.',
                'pt' => 'Um texto composto por muitas frases longas é difícil de ler, pois é mais difícil processar frases longas. Se mais de 25% das suas frases tiverem mais de 20 palavras, algumas dessas frases longas devem ser encurtadas.',
                'nl' => 'Een tekst die uit veel lange zinnen bestaat, is moeilijk te lezen omdat lange zinnen moeilijker te verwerken zijn. Als meer dan 25% van je zinnen langer dan 20 woorden zijn, moeten sommige van deze lange zinnen worden ingekort.',
                'ru' => 'Текст, состоящий из множества длинных предложений, трудно читать, потому что длинные предложения сложнее обрабатывать. Если более 25% ваших предложений содержат более 20 слов, некоторые из этих длинных предложений следует сократить.',
            ],

            'flesch_reading_ease' => [
                'en' => 'The Flesch Reading Ease check shows if your content is easy to read or not. The result is a number on a scale from 0 to 100 — the lower that number, the harder your text is to read. You should not use too many difficult words (it is usually believed that a good web text can be easily understood by a 13-15-year-old) and keep your sentences rather short to get a high result.',
                'fr' => 'La vérification Flesch Reading Ease indique si votre contenu est facile à lire ou non. Le résultat est un nombre sur une échelle de 0 à 100 - plus ce nombre est bas, plus votre texte est difficile à lire. Vous ne devez pas utiliser trop de mots difficiles (on pense généralement qu\'un bon texte Web peut être facilement compris par un adolescent de 13 à 15 ans) et garder vos phrases plutôt courtes pour obtenir un résultat élevé.',
                'es' => 'La Flesch Reading Ease muestra si su contenido es fácil de leer o no. El resultado es un número en una escala de 0 a 100: cuanto menor sea ese número, más difícil será leer el texto. No debe usar demasiadas palabras difíciles (por lo general, se cree que un texto web bueno puede ser fácilmente entendido por un niño de 13-15 años) y mantener sus oraciones bastante cortas para obtener un resultado alto.',
                'it' => 'Flesch Reading Ease mostra se il contenuto è facile da leggere o meno. Il risultato è un numero su una scala da 0 a 100: più basso è quel numero, più difficile sarà la lettura del testo. Non dovresti usare troppe parole difficili (di solito si crede che un buon testo web possa essere facilmente compreso da un 13-15 anni) e mantenere le frasi piuttosto brevi per ottenere un risultato elevato.',
                'de' => 'Die Flesch Reading Ease-Prüfung zeigt, ob Ihr Inhalt leicht zu lesen ist oder nicht. Das Ergebnis ist eine Zahl auf einer Skala von 0 bis 100 – je niedriger diese Zahl, desto schwieriger ist es, den Text zu lesen. Sie sollten nicht zu viele schwierige Wörter verwenden (es wird allgemein angenommen, dass ein guter Webtext von einem 13- bis 15-Jährigen leicht verstanden werden kann) und Ihre Sätze eher kurz halten, um ein gutes Ergebnis zu erzielen.',
                'pl' => 'Sprawdzian Flesch Reading Ease pokazuje, czy Twój tekst jest łatwy do przeczytania, czy nie. Wynik to liczba w skali od 0 do 100 — im niższa ta liczba, tym trudniejszy do przeczytania tekst. Nie należy używać zbyt wielu trudnych słów (zwykle uważa się, że dobry tekst internetowy może być łatwo zrozumiany przez 13-15-latka) i utrzymywać zdania raczej krótkie, aby uzyskać dobry wynik.',
                'cs' => 'Kontrola Flesch Reading Ease ukazuje, zda je váš obsah snadno čitelný, nebo ne. Výsledek je číslo na škále od 0 do 100 — čím nižší je toto číslo, tím těžší je čtení textu. Neměli byste používat příliš mnoho složitých slov (obvykle se předpokládá, že dobrý webový text je snadno pochopený 13–15letým dítětem) a měli byste udržovat věty spíše krátké, abyste dosáhli dobrého výsledku.',
                'pt' => 'A verificação de Facilidade de Leitura Flesch mostra se o seu conteúdo é fácil de ler ou não. O resultado é um número em uma escala de 0 a 100 — quanto menor esse número, mais difícil será ler o texto. Você não deve usar muitas palavras difíceis (geralmente acredita-se que um bom texto da web pode ser facilmente entendido por um adolescente de 13 a 15 anos) e deve manter suas frases relativamente curtas para obter um bom resultado.',
                'nl' => 'De Flesch Reading Ease controle laat zien of je inhoud gemakkelijk te lezen is of niet. Het resultaat is een getal op een schaal van 0 tot 100 — hoe lager dat getal, hoe moeilijker je tekst te lezen is. Je moet niet te veel moeilijke woorden gebruiken (het wordt meestal aangenomen dat een goede webtekst gemakkelijk te begrijpen is voor een 13-15-jarige) en probeer je zinnen kort te houden om een goed resultaat te behalen.',
                'ru' => 'Проверка Flesch Reading Ease показывает, легко ли читать ваш контент или нет. Результат — это число от 0 до 100: чем ниже это число, тем труднее читать текст. Не следует использовать слишком много сложных слов (обычно считается, что хороший веб-текст легко понимает 13-15-летний подросток) и старайтесь делать предложения короче для получения высокого результата.',
            ],

            'paragraph_length' => [
                'en' => 'We recommend that you should keep the length of your paragraphs below 150 words to ensure maximum comprehension while reading.',
                'fr' => 'Nous vous recommandons de garder la longueur de vos paragraphes en dessous de 150 mots pour assurer une compréhension maximale lors de la lecture.',
                'es' => 'Recomendamos que mantenga la longitud de sus párrafos por debajo de 150 palabras para garantizar la máxima comprensión mientras lee.',
                'it' => 'Si consiglia di mantenere la lunghezza dei paragrafi inferiore a 150 parole per garantire la massima comprensione durante la lettura.',
                'de' => 'Wir empfehlen, die Länge Ihrer Absätze unter 150 Wörter zu halten, um eine maximale Verständlichkeit beim Lesen zu gewährleisten.',
                'pl' => 'Zalecamy, aby długość akapitów nie przekraczała 150 słów, aby zapewnić maksymalną zrozumiałość podczas czytania.',
                'cs' => 'Doporučujeme, abyste délku svých odstavců udrželi pod 150 slovy pro zajištění maximálního porozumění při čtení.',
                'pt' => 'Recomendamos que mantenha a extensão dos seus parágrafos abaixo de 150 palavras para garantir a máxima compreensão durante a leitura.',
                'nl' => 'We raden aan om de lengte van je alinea\'s onder de 150 woorden te houden om maximale begrijpelijkheid tijdens het lezen te garanderen.',
                'ru' => 'Рекомендуем держать длину абзацев менее 150 слов, чтобы обеспечить максимальное понимание при чтении.',
            ],

            'passive_voice' => [
                'en' => 'You should keep the number of sentences containing passive voice under 10% of your total sentences. How about trying to write more active voice sentences instead?',
                'fr' => 'Vous devez limiter le nombre de phrases contenant une voix passive à moins de 10% du total de vos phrases. Que diriez-vous d\'essayer d\'écrire des phrases vocales plus actives à la place ?',
                'es' => 'Debe mantener el número de oraciones que contienen voz pasiva por debajo del 10% de sus oraciones totales. ¿Qué tal intentar escribir oraciones de voz más activas en su lugar?',
                'it' => 'Dovresti mantenere il numero di frasi contenenti voce passiva al di sotto del 10% delle frasi totali. Che ne dici di provare a scrivere frasi vocali più attive invece?',
                'de' => 'Sie sollten die Anzahl der Sätze mit Passivstimme auf weniger als 10 % Ihrer Gesamtsätze begrenzen. Wie wäre es, wenn Sie stattdessen mehr aktive Sätze schreiben?',
                'pl' => 'Powinieneś utrzymać liczbę zdań zawierających stronę bierną poniżej 10% wszystkich zdań. Co powiesz na próbę pisania więcej zdań w stronie czynnej?',
                'cs' => 'Měli byste omezit počet vět obsahujících trpný rod na méně než 10 % všech vašich vět. Co takhle zkusit napsat více vět v aktivním rodě?',
                'pt' => 'Você deve manter o número de frases com voz passiva abaixo de 10% do total de suas frases. Que tal tentar escrever mais frases na voz ativa?',
                'nl' => 'Je moet het aantal zinnen met de passieve vorm onder de 10% van je totale zinnen houden. Wat dacht je ervan om in plaats daarvan meer zinnen in de actieve vorm te schrijven?',
                'ru' => 'Вы должны ограничить количество предложений с пассивным залогом до 10% от общего числа предложений. Как насчет того, чтобы попробовать писать больше предложений в активном залоге?',
            ],

            'consecutive_sentences' => [
                'en' => 'A paragraph containing many consecutive sentences all starting with the same word is not a pleasant text. The words will be repeated and not fluent. Your text should not contains 3 or more sentences in a row all starting with the same word.',
                'fr' => 'Un paragraphe contenant de nombreuses phrases consécutives commençant toutes par le même mot n\'est pas un texte agréable. Les mots seront répétés et non fluents. Votre texte ne doit pas contenir 3 phrases ou plus d\'affilée commençant toutes par le même mot.',
                'es' => 'Un párrafo que contiene muchas oraciones consecutivas, todas comenzando con la misma palabra, no es un texto agradable. Las palabras serán repetidas y no fluidas. Su texto no debe contener 3 o más oraciones seguidas, todas comenzando con la misma palabra.',
                'it' => 'Un paragrafo contenente molte frasi consecutive che iniziano tutte con la stessa parola non è un testo piacevole. Le parole saranno ripetute e non fluenti. Il testo non deve contenere 3 o più frasi di fila che iniziano tutte con la stessa parola.',
                'de' => 'Ein Absatz, der viele aufeinanderfolgende Sätze enthält, die alle mit demselben Wort beginnen, ist kein angenehmer Text. Die Wörter werden wiederholt und nicht flüssig. Ihr Text sollte nicht 3 oder mehr Sätze hintereinander enthalten, die alle mit demselben Wort beginnen.',
                'pl' => 'Akapit zawierający wiele kolejnych zdań zaczynających się od tego samego słowa, nie jest przyjemnym tekstem. Słowa będą się powtarzać i nie będą płynne. Twój tekst nie powinien zawierać 3 lub więcej zdań z rzędu zaczynających się od tego samego słowa.',
                'cs' => 'Odstavec obsahující mnoho po sobě jdoucích vět, které všechny začínají stejným slovem, není příjemný text. Slova budou opakována a text nebude plynulý. Váš text by neměl obsahovat 3 nebo více vět za sebou, které začínají stejným slovem.',
                'pt' => 'Um parágrafo contendo muitas sentenças consecutivas todas começando com a mesma palavra não é um texto agradável. As palavras serão repetidas e não fluentes. Seu texto não deve conter 3 ou mais sentenças seguidas, todas começando com a mesma palavra.',
                'nl' => 'Een alinea met veel opeenvolgende zinnen die allemaal beginnen met hetzelfde woord is geen prettige tekst. De woorden zullen herhaald worden en niet vloeiend zijn. Je tekst mag niet 3 of meer zinnen achter elkaar bevatten die allemaal beginnen met hetzelfde woord.',
                'ru' => 'Абзац, содержащий множество последовательных предложений, начинающихся с одного и того же слова, не является приятным текстом. Слова будут повторяться, и текст будет не плавным. Ваш текст не должен содержать 3 или более предложений подряд, начинающихся с одного и того же слова.',
            ],
            'subheading_distribution' => [
                'en' => 'Subheading tags are indicators used in HTML to help structure your web page from an SEO point of view. Subheading tags range from H2 to H6 and form a hierarchical structure to your page. You should place a subheading above each long paragraph, or above a group of paragraphs that make up a thematic unit. Generally, the text following a subheading should not exceed 250-350 words.',
                'fr' => 'Les balises de sous-titres sont des indicateurs utilisés en HTML pour aider à structurer votre page Web d\'un point de vue SEO. Les balises de sous-titres vont de H2 à H6 et forment une structure hiérarchique à votre page. Vous devez placer un sous-titre au-dessus de chaque long paragraphe, ou au-dessus d\'un groupe de paragraphes qui composent une unité thématique. Généralement, le texte suivant une sous-titre ne doit pas dépasser 250-350 mots.',
                'es' => 'Las etiquetas de subtítulos son indicadores utilizados en HTML para ayudar a estructurar su página web desde un punto de vista de SEO. Las etiquetas de subtítulos varían de H2 a H6 y forman una estructura jerárquica para su página. Debe colocar un subtítulo sobre cada párrafo largo, o sobre un grupo de párrafos que componen una unidad temática. Generalmente, el texto que sigue a un subtítulo no debe exceder las 250-350 palabras.',
                'it' => 'I tag dei sottovoci sono indicatori utilizzati in HTML per aiutare a strutturare la tua pagina web da un punto di vista SEO. I tag dei sottovoci vanno da H2 a H6 e formano una struttura gerarchica per la tua pagina. È necessario posizionare una sottovoce sopra ogni lungo paragrafo o sopra un gruppo di paragrafi che compongono un\'unità tematica. In genere, il testo che segue una sottovoce non deve superare le 250-350 parole.',
                'de' => 'Unterüberschrift-Tags sind Indikatoren, die in HTML verwendet werden, um Ihre Webseite aus SEO-Sicht zu strukturieren. Unterüberschrift-Tags reichen von H2 bis H6 und bilden eine hierarchische Struktur auf Ihrer Seite. Sie sollten eine Unterüberschrift über jedem langen Absatz oder über einer Gruppe von Absätzen platzieren, die eine thematische Einheit bilden. In der Regel sollte der Text nach einer Unterüberschrift 250-350 Wörter nicht überschreiten.',
                'pl' => 'Tagi podtytułów to wskaźniki używane w HTML do pomocy w strukturalizacji Twojej strony internetowej z punktu widzenia SEO. Tagi podtytułów wahają się od H2 do H6 i tworzą hierarchiczną strukturę Twojej strony. Należy umieścić podtytuł nad każdym długim akapitem lub nad grupą akapitów, które tworzą jednostkę tematyczną. Zwykle tekst po podtytule nie powinien przekraczać 250-350 słów.',
                'cs' => 'Tagy podnadpisů jsou indikátory používané v HTML k tomu, aby pomohly strukturovat vaši webovou stránku z hlediska SEO. Tagy podnadpisů se pohybují od H2 do H6 a tvoří hierarchickou strukturu vaší stránky. Měli byste umístit podnadpis nad každý dlouhý odstavec nebo nad skupinu odstavců, které tvoří tematickou jednotku. Obecně platí, že text následující po podnadpisu by neměl přesáhnout 250-350 slov.',
                'pt' => 'As tags de subtítulos são indicadores usados no HTML para ajudar a estruturar sua página da web do ponto de vista do SEO. As tags de subtítulos vão de H2 a H6 e formam uma estrutura hierárquica para a sua página. Você deve colocar um subtítulo acima de cada parágrafo longo ou acima de um grupo de parágrafos que compõem uma unidade temática. Geralmente, o texto que segue um subtítulo não deve exceder 250-350 palavras.',
                'nl' => 'Subkop-tags zijn indicatoren die in HTML worden gebruikt om de structuur van je webpagina te helpen vanuit een SEO-oogpunt. Subkop-tags variëren van H2 tot H6 en vormen een hiërarchische structuur voor je pagina. Je moet een subkop boven elk lang alinea plaatsen, of boven een groep alinea\'s die een thematische eenheid vormen. Over het algemeen mag de tekst na een subkop niet meer dan 250-350 woorden bevatten.',
                'ru' => 'Теги подзаголовков — это индикаторы, используемые в HTML для помощи в структурировании вашей веб-страницы с точки зрения SEO. Теги подзаголовков варьируются от H2 до H6 и формируют иерархическую структуру вашей страницы. Вы должны разместить подзаголовок над каждым длинным абзацем или над группой абзацев, которые составляют тематическую единицу. Обычно текст, следующий за подзаголовком, не должен превышать 250-350 слов.',
            ],
            'transition_words' => [
                'en' => 'Transition words are words like ‘most importantly’, ‘because’, ‘therefore’, or ‘besides’. They help your text become easier to read. You should use transition words in at least 30% of the sentences in your text.',
                'fr' => 'Les mots de transition sont des mots comme « le plus important », « parce que », « donc » ou « en plus ». Ils facilitent la lecture de votre texte. Vous devez utiliser des mots de transition dans au moins 30% des phrases de votre texte.',
                'es' => 'Las palabras de transición son palabras como "lo más importante", "porque", "por lo tanto" o "además". Ayudan a que su texto sea más fácil de leer. Debe usar palabras de transición en al menos el 30% de las oraciones en su texto.',
                'it' => 'Le parole di transizione sono parole come "soprattutto", "perché", "quindi" o "oltre". Aiutano il tuo testo a diventare più facile da leggere. È necessario utilizzare parole di transizione in almeno il 30% delle frasi nel testo.',
                'de' => 'Übergangswörter sind Wörter wie „am wichtigsten“, „weil“, „deshalb“ oder „außerdem“. Sie helfen Ihrem Text, leichter zu lesen. Sie sollten in mindestens 30% der Sätze in Ihrem Text Übergangswörter verwenden.',
                'pl' => 'Słowa przejściowe to słowa takie jak „najważniejsze“, „ponieważ“, „dlatego“ lub „oprócz“. Pomagają one uczynić Twój tekst łatwiejszym do przeczytania. Powinieneś używać słów przejściowych w co najmniej 30% zdań w Twoim tekście.',
                'cs' => 'Přechodová slova jsou slova jako „nejdůležitější“, „protože“, „tudíž“ nebo „kromě toho“. Pomáhají vašemu textu stát se snadněji čitelným. Měli byste používat přechodová slova v alespoň 30% vět ve vašem textu.',
                'pt' => 'Palavras de transição são palavras como “mais importante”, “porque”, “portanto” ou “além disso”. Elas ajudam seu texto a se tornar mais fácil de ler. Você deve usar palavras de transição em pelo menos 30% das frases no seu texto.',
                'nl' => 'Overgangswoorden zijn woorden zoals "meest belangrijk", "omdat", "daarom" of "bovendien". Ze helpen je tekst gemakkelijker leesbaar te maken. Je moet overgangswoorden gebruiken in ten minste 30% van de zinnen in je tekst.',
                'ru' => 'Переходные слова — это слова, такие как «самое главное», «потому что», «поэтому» или «кроме того». Они помогают вашему тексту стать легче для чтения. Вы должны использовать переходные слова в как минимум 30% предложений вашего текста.',
            ],

            'single_h1' => [
                'en' => 'A H1 heading is the title of your [page_title] page and you should only use it once per page. For product page, the product name will be used as H1 heading. You should replace any H1 in your content that are not title of your [page_title] page with a lower heading level',
                'fr' => 'Un en-tête H1 est le titre de votre page [page_title] et vous ne devez l\'utiliser qu\'une seule fois par page. Pour la page du produit, le nom du produit sera utilisé comme en-tête H1. Vous devez remplacer tout H1 dans votre contenu qui n\'est pas le titre de votre page [page_title] par un niveau de titre inférieur',
                'es' => 'Un encabezado H1 es el título de su página [page_title] y solo debe usarlo una vez por página. Para la página del producto, el nombre del producto se utilizará como encabezado H1. Debe reemplazar cualquier H1 en su contenido que no sea el título de su página [page_title] con un nivel de encabezado más bajo',
                'it' => 'Un titolo H1 è il titolo della tua pagina [page_title] e dovresti usarlo solo una volta per pagina. Per la pagina del prodotto, il nome del prodotto verrà utilizzato come intestazione H1. Dovresti sostituire qualsiasi H1 nei tuoi contenuti che non sia il titolo della tua pagina [page_title] con un livello di intestazione inferiore',
                'de' => 'Eine H1-Überschrift ist der Titel Ihrer Seite [page_title] und sollte nur einmal pro Seite verwendet werden. Auf der Produktseite wird der Produktname als H1-Überschrift verwendet. Sie sollten jede H1 in Ihrem Inhalt, die nicht der Titel Ihrer Seite [page_title] ist, durch eine niedrigere Überschriftsebene ersetzen',
                'pl' => 'Nagłówek H1 to tytuł strony [page_title] i powinien być używany tylko raz na stronę. Dla strony produktu nazwa produktu będzie używana jako nagłówek H1. Należy zastąpić wszelkie nagłówki H1 w treści, które nie są tytułem strony [page_title], nagłówkiem niższego poziomu',
                'cs' => 'Hlava H1 je název vaší stránky [page_title] a měla by být použita pouze jednou na stránce. Na stránce produktu bude název produktu použit jako H1. Měli byste nahradit jakoukoli H1 ve vašem obsahu, která není názvem vaší stránky [page_title], nižší úrovní nadpisu',
                'pt' => 'Um título H1 é o título da sua página [page_title] e deve ser usado apenas uma vez por página. Para a página do produto, o nome do produto será usado como título H1. Você deve substituir qualquer H1 no seu conteúdo que não seja o título da sua página [page_title] por um nível de título inferior',
                'nl' => 'Een H1-kop is de titel van je [page_title]-pagina en je mag deze maar één keer per pagina gebruiken. Voor de productpagina wordt de productnaam als H1-kop gebruikt. Je moet elke H1 in je inhoud die niet de titel van je [page_title]-pagina is, vervangen door een lagere kopniveau',
                'ru' => 'Заголовок H1 — это заголовок вашей страницы [page_title], и его следует использовать только один раз на странице. Для страницы продукта название продукта будет использовано как заголовок H1. Вы должны заменить любой H1 в вашем контенте, который не является заголовком вашей страницы [page_title], на заголовок более низкого уровня',
            ],
            'product_name' => [
                'en' => 'Product name',
                'fr' => 'Nom du produit',
                'es' => 'Nombre del producto',
                'it' => 'Nome del prodotto',
                'de' => 'Produktname',
                'pl' => 'Nazwa produktu',
                'cs' => 'Název produktu',
                'pt' => 'Nome do produto',
                'nl' => 'Productnaam',
                'ru' => 'Название продукта',
            ],
            'category_name' => [
                'en' => 'Category name',
                'fr' => 'Nom de catégorie',
                'es' => 'Nombre de la categoría',
                'it' => 'Nome della categoria',
                'de' => 'Kategoriename',
                'pl' => 'Nazwa kategorii',
                'cs' => 'Název kategorie',
                'pt' => 'Nome da categoria',
                'nl' => 'Categorie naam',
                'ru' => 'Название категории',
            ],

            'cms_title' => [
                'en' => 'CMS title',
                'fr' => 'Titre CMS',
                'es' => 'Título de CMS',
                'it' => 'Titolo CMS',
                'de' => 'CMS-Titel',
                'pl' => 'Tytuł CMS',
                'cs' => 'Titulek CMS',
                'pt' => 'Título do CMS',
                'nl' => 'CMS-titel',
                'ru' => 'Заголовок CMS',
            ],
            'cms_category_title' => [
                'en' => 'CMS category title',
                'fr' => 'Titre de la catégorie CMS',
                'es' => 'Título de categoría de CMS',
                'it' => 'Titolo della categoria CMS',
                'de' => 'CMS-Kategorietitel',
                'pl' => 'Tytuł kategorii CMS',
                'cs' => 'Titulek kategorie CMS',
                'pt' => 'Título da categoria CMS',
                'nl' => 'CMS-categorie titel',
                'ru' => 'Заголовок категории CMS',
            ],
            'meta_title' => [
                'en' => 'Page title',
                'fr' => 'Titre de la page',
                'es' => 'Título de la página',
                'it' => 'Titolo della pagina',
                'de' => 'Seitentitel',
                'pl' => 'Tytuł strony',
                'cs' => 'Titulek stránky',
                'pt' => 'Título da página',
                'nl' => 'Paginatitel',
                'ru' => 'Заголовок страницы',
            ],
            'manufacturer_name' => [
                'en' => 'Brand (manufacturer) name',
                'fr' => 'Nom de la marque (fabricant)',
                'es' => 'Nombre de la marca (fabricante)',
                'it' => 'Nome del marchio (produttore)',
                'de' => 'Markenname (Hersteller)',
                'pl' => 'Nazwa marki (producenta)',
                'cs' => 'Název značky (výrobce)',
                'pt' => 'Nome da marca (fabricante)',
                'nl' => 'Merknaam (fabrikant)',
                'ru' => 'Название бренда (производителя)',
            ],
            'supplier_name' => [
                'en' => 'Supplier name',
                'fr' => 'Nom du fournisseur',
                'es' => 'Nombre del proveedor',
                'it' => 'Nome del fornitore',
                'de' => 'Lieferantenname',
                'pl' => 'Nazwa dostawcy',
                'cs' => 'Název dodavatele',
                'pt' => 'Nome do fornecedor',
                'nl' => 'Leveranciersnaam',
                'ru' => 'Название поставщика',
            ],

        ];
    }

    public static function trans($key, $id_lang = null)
    {
        if (!$id_lang) {
            $isoCode = Ets_Seo::getContextStatic()->language->iso_code;
        } else {
            $isoCode = Language::getIsoById($id_lang);
        }
        $data = self::dataTrans();
        if (isset($data[$key][$isoCode])) {
            return $data[$key][$isoCode];
        }

        $lang_default = (int) Configuration::get('PS_LANG_DEFAULT');
        $isoCode = Language::getIsoById($lang_default);
        if (isset($data[$key][$isoCode])) {
            return $data[$key][$isoCode];
        }

        if (isset($data[$key]['en'])) {
            return $data[$key]['en'];
        }

        return '';
    }

    public static function getAllTrans($id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = Ets_Seo::getContextStatic()->language->id;
        }

        $data = self::dataTrans();
        $result = [];
        foreach ($data as $key => $item) {
            $result[$key] = self::trans($key, $id_lang);
        }

        return $result;
    }
}
