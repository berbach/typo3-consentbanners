<?php

namespace Bb\ConsentBanner\Controller;

use Bb\ConsentBanner\Domain\Model\Consent;
use Bb\ConsentBanner\Domain\Repository\ConsentRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class AjaxController extends ActionController
{
    public function __construct(
        protected ConsentRepository $consentRepository,
        protected ResponseFactoryInterface $responseFactory
    ) {}

    public function mainAction(): ResponseInterface
    {
        DebuggerUtility::var_dump($this->request->getQueryParams());
//        $raw = $this->request->getHttpRequest()->getBody()->getContents();
//        $payload = json_decode($raw, true);

//        if(empty($payload['name']) || empty($payload['message'])) {
//            return $this->json(['error' => 'Name and message are required']);
//        }

//        $consent = new Consent();
//
//        $this->consentRepository->add($consent);

        return $this->json(['saved' => true]);
    }

    /**
     * @throws IllegalObjectTypeException
     */
    public function writeAction(): ResponseInterface
    {
//        $raw = $this->request->getHttpRequest()->getBody()->getContents();
//        $payload = json_decode($raw, true);

//        if(empty($payload['name']) || empty($payload['message'])) {
//            return $this->json(['error' => 'Name and message are required']);
//        }

//        $consent = new Consent();
//
//        $this->consentRepository->add($consent);

        return $this->json(['saved' => true]);
    }

    public function readAction(?string $identificationKey = null): ResponseInterface
    {
        if ($identificationKey !== null) {
            $consent = $this->consentRepository->findByIdentificationKey($identificationKey);
            return $this->json(['consent'=> $consent]);
        }

        return $this->json(['consent'=> null ]);
    }

    private function json(array $data): ResponseInterface
    {
        $response = $this->responseFactory->createResponse()->withHeader('Content-Type','application/json');
        $response->getBody()->write(json_encode($data));
        return $response;
    }
}