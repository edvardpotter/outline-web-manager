<?php

namespace App\Controller\Admin;

use App\DTO\Admin\ServerKey;
use App\Form\Admin\ServerKeys\ServerKeyType;
use App\Repository\ServerRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use OutlineManagerClient\OutlineClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ServerKeysController extends AbstractDashboardController
{
    public function __construct(private readonly ServerRepository $serverRepository)
    {
    }

    #[Route('/admin/server/{serverId}/keys', name: 'admin_server_keys')]
    public function keys(int $serverId): Response
    {
        $server = $this->serverRepository->find($serverId);
        if ($server === null) {
            throw $this->createNotFoundException('Server not found');
        }

        try {
            $outlineClient = $this->getOutlineClient($server->getUrl());
        } catch (BadRequestHttpException) {
            return $this->render('admin/exception/server_is_not_available.html.twig', [
                'server' => $server,
            ]);
        }

        return $this->render('admin/server_keys/index.html.twig', [
            'server' => $server,
            'keys' => $outlineClient->getKeys()
        ]);
    }

    #[Route('/admin/server/{serverId}/keys/{id}/delete', name: 'admin_server_keys_delete')]
    public function delete(int $serverId, int $id): Response
    {
        $server = $this->serverRepository->find($serverId);
        if ($server === null) {
            throw $this->createNotFoundException('Server not found');
        }

        try {
            $outlineClient = $this->getOutlineClient($server->getUrl());
        } catch (BadRequestHttpException) {
            return $this->render('admin/exception/server_is_not_available.html.twig', [
                'server' => $server,
            ]);
        }

        $outlineClient->deleteKey($id);

        return $this->redirectToRoute('admin', [
            'routeName' => 'admin_server_keys',
            'routeParams' => [
                'serverId' => $serverId
            ]
        ]);
    }

    #[Route('/admin/server/{serverId}/keys/add', name: 'admin_server_keys_add')]
    public function add(int $serverId, Request $request): Response
    {
        $server = $this->serverRepository->find($serverId);
        if ($server === null) {
            throw $this->createNotFoundException('Server not found');
        }

        $serverKey = new ServerKey();

        $form = $this->createForm(ServerKeyType::class, $serverKey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $outlineClient = $this->getOutlineClient($server->getUrl());
            } catch (BadRequestHttpException) {
                return $this->render('admin/exception/server_is_not_available.html.twig', [
                    'server' => $server,
                ]);
            }

            $keyType = $outlineClient->addKey($serverKey->name);
            if ($serverKey->dataLimit !== null) {
                $outlineClient->setKeyDataLimit($keyType->getId(), $serverKey->dataLimit * 1000);
            }

            return $this->redirectToRoute('admin', [
                'routeName' => 'admin_server_keys',
                'routeParams' => [
                    'serverId' => $serverId
                ]
            ]);
        }

        return $this->render('admin/server_keys/form.html.twig', [
            'form' => $form->createView(),
            'server' => $server
        ]);
    }

    #[Route('/admin/server/{serverId}/keys/{id}/edit', name: 'admin_server_keys_edit')]
    public function edit(int $serverId, int $id, Request $request): Response
    {
        $server = $this->serverRepository->find($serverId);
        if ($server === null) {
            throw $this->createNotFoundException('Server not found');
        }

        try {
            $outlineClient = $this->getOutlineClient($server->getUrl());
        } catch (BadRequestHttpException) {
            return $this->render('admin/exception/server_is_not_available.html.twig', [
                'server' => $server,
            ]);
        }

        $keyType = $outlineClient->getKeyById($id);
        if ($keyType === null) {
            throw $this->createNotFoundException('Key not found');
        }

        $serverKey = new ServerKey();
        $serverKey->name = $keyType->getName();
        $serverKey->dataLimit = ($keyType->getDataLimit()['bytes'] ?? 0) / 1000;

        $form = $this->createForm(ServerKeyType::class, $serverKey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($serverKey->name !== null) {
                $outlineClient->setKeyName($keyType->getId(), $serverKey->name);
            }

            if ($serverKey->dataLimit !== null) {
                $outlineClient->setKeyDataLimit($keyType->getId(), $serverKey->dataLimit * 1000);
            } else {
                $outlineClient->unsetKeyDataLimit($keyType->getId());
            }

            return $this->redirectToRoute('admin', [
                'routeName' => 'admin_server_keys',
                'routeParams' => [
                    'serverId' => $serverId
                ]
            ]);
        }

        return $this->render('admin/server_keys/form.html.twig', [
            'form' => $form->createView(),
            'server' => $server
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
