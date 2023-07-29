<?php

namespace App\Controller\Admin;

use App\Entity\Server;
use App\Validator\ServerUrl;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use OutlineManagerClient\OutlineClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ServerCrudController extends AbstractCrudController
{
    public function __construct()
    {
    }

    public static function getEntityFqcn(): string
    {
        return Server::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('url')
                ->setFormTypeOption(
                'attr',
                [
                    'placeholder' => 'https://{ip}:{port}/{secretString}/'
                ])
                ->setFormTypeOption(
                    'constraints',
                    [
                        new ServerUrl()
                    ]
                )
            ,
            AssociationField::new('owner')
                ->autocomplete()
            ,
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $keysAction = Action::new('viewKeys', 'Keys')
            ->displayAsButton()
            ->displayAsLink()
            ->linkToRoute('admin_server_keys', function (Server $server): array {
                return [
                    'serverId' => $server->getId(),
                ];
            })
        ;

        $serverInfoAction = Action::new('viewServerInfo', 'Info')
            ->displayAsButton()
            ->displayAsLink()
            ->linkToCrudAction('viewServerInfo');

        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $keysAction)
            ->add(Crud::PAGE_DETAIL, $keysAction)
            ->add(Crud::PAGE_DETAIL, $serverInfoAction)
            ->add(Crud::PAGE_INDEX, $serverInfoAction)
            ;
    }

    public function viewServerInfo(AdminContext $context): Response
    {
        /** @var Server $server */
        $server = $context->getEntity()->getInstance();

        try {
            $outlineClient = $this->getOutlineClient($server->getUrl());
        } catch (BadRequestHttpException) {
            return $this->render('admin/exception/server_is_not_available.html.twig', [
                'server' => $server,
            ]);
        }

        return $this->render('admin/server/server_info.html.twig', [
            'server' => $server,
            'info' => $outlineClient->getServer()
        ]);
    }

    private function getOutlineClient(string $url): OutlineClient
    {
        $outlineClient = new OutlineClient($url);

        try {
            $outlineClient->getServer();
        } catch (\Exception) {
            throw new BadRequestHttpException('Server is not available');
        }

        return $outlineClient;
    }
}
