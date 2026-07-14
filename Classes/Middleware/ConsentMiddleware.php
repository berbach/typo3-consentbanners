<?php
namespace Bb\ConsentBanner\Middleware;

use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Bb\ConsentBanner\Domain\Repository\ConsentLogRepository;

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

        if (!isset($data['services']) || !is_array($data['services']) || empty($data['hash'])) {
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        }

        // The consent log is site-scoped: store which root page it belongs to.
        $site = $request->getAttribute('site');
        $rootPageId = $site instanceof Site ? $site->getRootPageId() : 0;

        $consentLogRepository = GeneralUtility::makeInstance(ConsentLogRepository::class);
        $consentLogRepository->save((string)$data['hash'], (int)($data['version'] ?? 0), $data['services'], $rootPageId);

        return new JsonResponse(['success' => true]);

    }
}
