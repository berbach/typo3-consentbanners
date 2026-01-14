<?php
namespace Bb\ConsentBanner\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Symfony\Component\Uid\Uuid;
use Bb\ConsentBanner\Domain\Repository\ConsentRepository;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

final class ConsentMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() !== '/consent/save') {
            return $handler->handle($request);
        }
        $data = json_decode((string)$request->getBody(), true);
//        if (!isset($data['services']) || !is_array($data['services'])) {
//        }

        $uuid = $_COOKIE['consent_uuid'];
        //$repo = GeneralUtility::makeInstance(ConsentRepository::class);
        //$repo->save($uuid, 1, $data['services']);

        return (new JsonResponse(['success' => true]))
            ->withAddedHeader('Set-Cookie', 'consent_uuid=' . $uuid . '; Path=/; SameSite=Lax');
    }
}
