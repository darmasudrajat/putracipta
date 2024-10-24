<?= "<?php\n" ?>

namespace App\Controller<?php if ($vars['entityNamespace'] !== ''): ?>\<?php endif ?><?= $vars['entityNamespace'] ?>;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\<?= $vars['entityFullName'] ?>;
use App\Form\<?= $vars['entityFullName'] ?>Type;
use App\Grid\<?= $vars['entityFullName'] ?>GridType;
use App\Repository\<?= $vars['entityFullName'] ?>Repository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/<?= $vars['templatePathPrefix'] ?>')]
class <?= $vars['entityName'] ?>Controller extends AbstractController
{
    #[Route('/_list', name: '<?= $vars['routeNamePrefix'] ?>__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, <?= $vars['entityName'] ?>Repository $<?= $vars['instanceNameSingular'] ?>Repository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(<?= $vars['entityName'] ?>GridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $<?= $vars['instanceNamePlural'] ?>) = $<?= $vars['instanceNameSingular'] ?>Repository->fetchData($criteria);

        return $this->renderForm("<?= $vars['templatePathPrefix'] ?>/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            '<?= $vars['instanceNamePlural'] ?>' => $<?= $vars['instanceNamePlural'] ?>,
        ]);
    }

    #[Route('/', name: '<?= $vars['routeNamePrefix'] ?>_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("<?= $vars['templatePathPrefix'] ?>/index.html.twig");
    }

    #[Route('/new', name: '<?= $vars['routeNamePrefix'] ?>_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, <?= $vars['entityName'] ?>Repository $<?= $vars['instanceNameSingular'] ?>Repository): Response
    {
        $<?= $vars['instanceNameSingular'] ?> = new <?= $vars['entityName'] ?>();
        $form = $this->createForm(<?= $vars['entityName'] ?>Type::class, $<?= $vars['instanceNameSingular'] ?>);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $<?= $vars['instanceNameSingular'] ?>Repository->add($<?= $vars['instanceNameSingular'] ?>, true);

            return $this->redirectToRoute('<?= $vars['routeNamePrefix'] ?>_show', ['id' => $<?= $vars['instanceNameSingular'] ?>->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('<?= $vars['templatePathPrefix'] ?>/new.html.twig', [
            '<?= $vars['instanceNameSingular'] ?>' => $<?= $vars['instanceNameSingular'] ?>,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '<?= $vars['routeNamePrefix'] ?>_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(<?= $vars['entityName'] ?> $<?= $vars['instanceNameSingular'] ?>): Response
    {
        return $this->render('<?= $vars['templatePathPrefix'] ?>/show.html.twig', [
            '<?= $vars['instanceNameSingular'] ?>' => $<?= $vars['instanceNameSingular'] ?>,
        ]);
    }

    #[Route('/{id}/edit', name: '<?= $vars['routeNamePrefix'] ?>_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, <?= $vars['entityName'] ?> $<?= $vars['instanceNameSingular'] ?>, <?= $vars['entityName'] ?>Repository $<?= $vars['instanceNameSingular'] ?>Repository): Response
    {
        $form = $this->createForm(<?= $vars['entityName'] ?>Type::class, $<?= $vars['instanceNameSingular'] ?>);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $<?= $vars['instanceNameSingular'] ?>Repository->add($<?= $vars['instanceNameSingular'] ?>, true);

            return $this->redirectToRoute('<?= $vars['routeNamePrefix'] ?>_show', ['id' => $<?= $vars['instanceNameSingular'] ?>->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('<?= $vars['templatePathPrefix'] ?>/edit.html.twig', [
            '<?= $vars['instanceNameSingular'] ?>' => $<?= $vars['instanceNameSingular'] ?>,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '<?= $vars['routeNamePrefix'] ?>_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, <?= $vars['entityName'] ?> $<?= $vars['instanceNameSingular'] ?>, <?= $vars['entityName'] ?>Repository $<?= $vars['instanceNameSingular'] ?>Repository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $<?= $vars['instanceNameSingular'] ?>->getId(), $request->request->get('_token'))) {
            $<?= $vars['instanceNameSingular'] ?>Repository->remove($<?= $vars['instanceNameSingular'] ?>, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('<?= $vars['routeNamePrefix'] ?>_index', [], Response::HTTP_SEE_OTHER);
    }
}
