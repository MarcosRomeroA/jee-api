<?php declare(strict_types=1);

namespace App\Apps\Web\Post\AddPostTempResource;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Domain\PostResource;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddPostTemporaryResourceController extends ApiController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request, string $id, string $sessionId): Response
    {
        /** @var UploadedFile $resource */
        $data = $request->request->all();

        PostResource::checkIsValidResourceType($data['type']);

        $resource = $request->files->get('resource');

        $fileName = (new Uuid($data['id']))->value() . '.' . $resource->getClientOriginalExtension();
        $tempFolder = '/var/tmp/resource/'.(new \DateTimeImmutable())->format('Ymd').'/'.$id.'/'.$data['type'];
        $uploadDir = $this->getParameter('kernel.project_dir') . $tempFolder;
        $resource->move($uploadDir, $fileName);

        return $this->successEmptyResponse(Response::HTTP_ACCEPTED);
    }
}