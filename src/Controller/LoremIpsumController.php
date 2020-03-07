<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LoremIpsumController extends AbstractController
{
    /**
     * @Route("/about", name="about")
     * @Route("/privacy", name="privacy")
     * @Route("/faq", name="faq")
     * @Route("/delivery", name="delivery")
     * @Route("/terms", name="terms")
     * @Route("/returns", name="returns")
     */
    public function index($_route)
    {
        switch ($_route) {
            case 'faq':
                $title = 'FAQ';
                break;
            case 'terms':
                $title = 'Terms & Conditions';
                break;
            default:
                $title = ucfirst($_route);
                break;
        }

        $ipsum[] = <<<'EOD'
Donec efficitur vitae justo a egestas. Fusce consequat neque elit, in 
ultricies orci placerat ac. Donec ullamcorper, ipsum vel efficitur ornare,
tellus risus fermentum odio, vitae egestas nunc turpis ac neque. Cras non 
mattis eros. Vivamus commodo nulla vitae lacus ultrices mollis. Nulla turpis 
ligula, accumsan sed bibendum id, sollicitudin eu neque. Vivamus faucibus 
arcu nunc, sit amet bibendum nisl vehicula eu.Nunc mattis rutrum risus, nec 
tincidunt arcu consectetur ut. Aliquam egestas dolor sed metus ultrices luctus. 
Maecenas eu tempor dolor. Nam sed tristique felis. Aliquam vitae pellentesque 
odio. Quisque non dignissim ligula. Donec sed viverra arcu. 
EOD;

        $ipsum[] = <<<'EOD'
Etiam aliquet massa sit amet 
nunc ullamcorper, non tristique orci volutpat. In hac habitasse platea 
dictumst. Maecenas rhoncus nunc eget purus pretium venenatis. Suspendisse 
auctor, ipsum eu commodo posuere, mi massa rhoncus ex, at dapibus 
tortor massa a enim. Fusce iaculis aliquam vulputate. Quisque a libero s
it amet mauris dapibus pellentesque sed dapibus risus. Morbi efficitur ligula 
sagittis condimentum mattis. Cras a nisi id leo volutpat malesuada. Cras nulla 
justo, cursus vitae varius et, vulputate in orci. Suspendisse sed libero non 
arcu porta pretium in eu arcu. Phasellus sagittis tortor sit amet tristique 
volutpat. Nam molestie ultricies eros a pulvinar. Phasellus id accumsan lorem. 
Sed nec ipsum non elit lobortis mattis.  
EOD;

        $ipsum[] = <<<'EOD'
Cras dapibus facilisis eros, interdum interdum tellus eleifend consequat. Maecenas 
faucibus tellus vitae orci consequat, et accumsan risus sollicitudin. 
Morbi nec facilisis elit. In et lorem ut sapien eleifend suscipit eu vel velit. 
Donec eu nulla id felis pharetra malesuada. Vivamus viverra mauris in est tempor 
dignissim. Integer interdum magna eros, eu cursus ligula feugiat vel.
Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo 
minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis 
dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum 
necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non 
recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis
oluptatibus maiores alias consequatur aut perferendis doloribus asperiores 
repellat. 
EOD;

        $ipsum[] = <<<'EOD'
Etiam tellus nisl, ornare vel volutpat sodales, ullamcorper a leo.
Etiam consequat augue finibus massa aliquam tincidunt. Proin posuere risus 
lacinia, elementum risus at, rhoncus ligula. 
Etiam vehicula, sapien eget bibendum hendrerit, nunc lectus aliquet 
sapien, non iaculis ante elit ac ligula. Praesent aliquam, mi ac consectetur 
tincidunt, arcu felis egestas nibh, sit amet mattis nibh tortor sit amet est. 
Quisque non velit interdum, dictum arcu sit amet, placerat erat. Aliquam gravida, 
nisl in vestibulum viverra, velit libero vestibulum ex, a feugiat urna magna 
quis sem. Donec id imperdiet erat, vitae molestie erat. Fusce sollicitudin 
ullamcorper pulvinar. Suspendisse potenti. Maecenas eu pharetra ante. Duis 
porta ante at rhoncus ullamcorper. Donec purus purus, blandit at commodo 
id, pulvinar in velit. Fusce pulvinar arcu neque, at suscipit justo hendrerit 
venenatis. Aenean auctor ultrices libero eget euismod. Fusce vitae metus et 
dui laoreet iaculis. Nulla condimentum est fermentum interdum imperdiet. 
In ante nulla, tempor eu tortor quis, interdum lacinia ex. 
EOD;

        $ipsum[] = <<<'EOD'
Ut hendrerit, dui non maximus dignissim, felis libero rutrum ipsum, sed 
venenatis lectus risus eget turpis. Pellentesque feugiat neque lectus, 
quis tincidunt enim placerat id. Phasellus ultrices luctus felis ac dapibus. 
Sed viverra mi vel orci malesuada tempus. In varius commodo neque, non 
ultrices elit tincidunt ut. In dui felis, porta nec quam sed, consequat 
ullamcorper libero. Praesent ac eros eget lacus pulvinar dignissim varius ac 
ipsum. Morbi vel nisi ornare, venenatis ipsum sed, condimentum elit. Orci 
varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus 
mus. Sed consequat odio urna, in tempor tellus molestie at. Proin mi nisl, 
rutrum sed nulla et, consequat mollis ante. In diam neque, consequat eu 
convallis vel, fermentum tincidunt sem. Mauris consectetur tempus suscipit. 
Sed aliquet nulla a porttitor pretium. Aenean interdum mauris neque, a 
ullamcorper justo aliquet vitae. Suspendisse posuere feugiat lectus et iaculis. 
EOD;

        $ipsum[] = <<<'EOD'
Mauris suscipit dolor sed dignissim euismod. Fusce non augue sit amet ipsum 
dignissim vulputate. Praesent non magna dui. Ut ut tristique leo. Nulla 
id sagittis urna. Curabitur sed nibh non justo bibendum vulputate in 
consectetur mauris. Sed mattis at nibh non auctor. Aliquam id pharetra 
sapien, a aliquam elit. Aenean sodales venenatis diam, ac laoreet nulla 
feugiat ac. Etiam ac erat ut odio egestas sodales vel sit amet elit. 
Nullam luctus pharetra nunc, lacinia efficitur velit tristique id. Duis vitae 
consequat ante. Cras ut dictum eros, non dignissim nisl. Donec blandit 
malesuada mi, quis sollicitudin purus dapibus quis. Morbi ullamcorper justo 
acinia mauris blandit, non ullamcorper mauris convallis. Suspendisse quis 
ante id odio tempus ullamcorper. In quis neque fermentum, tristique libero 
nec, dapibus orci. Praesent quis leo sit amet mauris dignissim aliquet eget 
mattis ligula. Nullam sit amet enim a felis gravida dapibus in a leo. 
Phasellus sed lacus enim. 
EOD;

        shuffle($ipsum);

        $paragraphs = [];
        for ($i = 1; $i <= 3; $i++) {
            $paragraphs[] = $ipsum[$i];
        }

        return $this->render('lorem_ipsum/index.html.twig', [
            'title' => $title,
            'paragraphs' => $paragraphs
        ]);
    }
}
