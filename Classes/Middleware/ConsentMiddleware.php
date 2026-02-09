<?php
namespace Bb\ConsentBanner\Middleware;

use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Symfony\Component\Uid\Uuid;
use Bb\ConsentBanner\Domain\Repository\ConsentLogRepository;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

final class ConsentMiddleware implements MiddlewareInterface
{
    /**
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() !== '/api/consent/save') {
            return $handler->handle($request);
        }
        $data = json_decode((string)$request->getBody(), true);

        if(!isset($data['services']) || !is_array($data['services'])){
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        }

        $consentLogRepository = GeneralUtility::makeInstance(ConsentLogRepository::class);
        $consentLogRepository->save($data['hash'], $data['version'], $data['services']);
        return new JsonResponse(['success' => true]);

    }
}
