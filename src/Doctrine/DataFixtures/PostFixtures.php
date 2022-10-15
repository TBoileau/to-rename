<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Doctrine\Entity\Post;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

final class PostFixtures extends Fixture
{
    use FakerTrait;

    public function __construct(private readonly string $uploadDir)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $states = ['draft', 'reviewed', 'rejected', 'published'];

        for ($index = 1; $index <= 50; ++$index) {
            $post = new Post();
            $post->setTitle($this->faker()->sentence());
            $post->setContent(self::randomMarkdown());

            /** @var string $excerpt */
            $excerpt = $this->faker()->paragraphs(3, true);

            $post->setExcerpt($excerpt);

            $filename = sprintf('%s.png', Uuid::v4());
            copy(
                sprintf('%s/image.png', $this->uploadDir),
                sprintf('%s/%s', $this->uploadDir, $filename)
            );

            $post->setCover($filename);
            $post->setSlug(sprintf('post-%d', $index));

            $post->setState($states[$index % 4]);

            if ('published' === $post->getState()) {
                $post->setPublishedAt(new DateTimeImmutable());
            }

            $manager->persist($post);
        }

        $manager->flush();
    }

    public static function randomMarkdown(): string
    {
        return <<<EOF
# Sparsus caede temptabimus ferventi suo securiferumque Ennomon

## Voce que vetuere vobis quoque fecit robora

Lorem markdownum viris auras caelo cruoris tetigit inultos verso omne. **Vel**
proelia aditus cui Dindymaque radice concussaque molitur querno nullus. Pereunt
circumque [verbere](http://acoetes.org/), ego nova admirabile equa toris dixit
Atalanta corve. At crinem aquatica *retexuit contigerant* marinae puer
[Melaneus](http://similisque-et.io/): aer ad. Dat data indicat pallidus, aequora
et crinem caloris nondum argenti vindicat, magnum Apollineas Pyramus et inferna,
non.

1. Ferro iuris mentis
2. Geminos postulat refecta addendum concilio quamquam
3. Marsya mixta
4. Cur secreta iugo

Fusus deae gravius facti, qui et minuuntur annis tum, ita mihi iam. Praestent
deferre; dis cepit, et minasque tempusque conplexibus prius: meo est aureus.
Annis innocuos illam, vibrantia dentibus fugit mirata cum paulum. Crinis pontus
rata neque **cui amictu dumque** librat stellatus perque decent cingens primaque
euntem exhortantur ipsa. Ad madidum.

## Prohibetque lacus

Quo cervice nympharum in montibus equarum. Diros magna, Amymonen edentem
[Acrisium parmam](http://sed.com/gladii.html), quid, vetustas pedes. Forma nec
aderamus os, ibi ira Cassiope vestes licet Cebrenida Priapi somnus cessastis
digitis addit relinquam neque qui.

- Illo paene cornibus leoni date sine iam
- Meo ergo parenti nec visus
- Hunc vicit vincis signo

## Excidit vaticinos

Salutet sed sideribus tela genitore igne. Non Menoeten humus ut potens, hac
victima monti; **in** summa. Furtim rigido precor talia, qui unguibus parte
lactantiaque movent firmat partem lumina illa, caput quae. Sed et erat Semiramio
nomen illam, clamor pectusque corpora.
```php
<?php

declare(strict_types=1);

namespace App;

class Foo 
{
    public function bar(): void
    {
        // ...
    }
}
```
Deusve radii Almo sacri, ad pete, nec Medusae. Labore sceptri crepitantia ipsa.
Tibi tulit volucri spondere sceleris ut fleturi prius aquas rubet nimium; est
est tempus adfligi pluribus notavi ea. Sopor diverso vera at torpet: formae quem
nullos nec movi, in.

Cratera simul, nocte est exactum redeunt ausa medullis quoque
[et](http://olympi.net/venditviolentus) grave spatium cratera monstri pelle non
fundamina. Attonitum phaethon relinque missi inscripsere illis moderatior
trepidat. Tam cruorem isse, et in et quae, vicisse turbae frondibus victus
plumeus oppugnare gravitate tu pater, tota. Quae erat quaque caelo; et claudit,
ara Aetnen cum praepes cepit natae non: plumis imagine Hyperione!
EOF;
    }
}
