<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Symfony;

use App\Contexts\Shared\Domain\Exception\ValidationException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract readonly class BaseRequest
{
    /**
     * @throws ReflectionException
     */
    public function __construct(
        protected ValidatorInterface $validator,
        protected RequestStack $requestStack
    )
    {
        $this->populate();
        $this->validate();
    }

    public function validate(): void
    {
        $errors = $this->validator->validate($this);

        $errorMessages = [];

        foreach ($errors as $message) {
            $errorMessages[] = [$message->getPropertyPath() => $message->getMessage()];
        }

        if (count($errorMessages) > 0) {
            throw new ValidationException($errorMessages);
        }
    }

    public function getRequest(): array
    {
        $request = $this->requestStack->getCurrentRequest() ?? Request::createFromGlobals();
        $q = $this->getCriteria($request->query->get('q'));

        try{
            $requestData = $request->toArray();
            // Solo establecer valores por defecto si la clase hijo tiene la propiedad 'q'
            if ($this->hasQProperty()) {
                if ($q === null) {
                    $q = [];
                }
                $q['limit'] = isset($q['limit']) ? (int)$q['limit'] : 10;
                $q['offset'] = isset($q['offset']) ? (int)$q['offset'] : 0;
                $requestData['q'] = $q;
            } elseif ($q) {
                $requestData['q'] = $q;
            }
            return $requestData;
        }
        catch (\Exception){
            $requestData = [];
            // Solo establecer valores por defecto si la clase hijo tiene la propiedad 'q'
            if ($this->hasQProperty()) {
                if ($q === null) {
                    $q = [];
                }
                $q['limit'] = isset($q['limit']) ? (int)$q['limit'] : 10;
                $q['offset'] = isset($q['offset']) ? (int)$q['offset'] : 0;
                $requestData['q'] = $q;
            } elseif ($q) {
                $requestData['q'] = $q;
            }
            return $requestData;
        }
    }

    /**
     * @throws ReflectionException
     */
    protected function populate(): void
    {
        $requestData = $this->getRequest();
        $reflectionClass = new ReflectionClass($this);

        foreach ($requestData as $property => $value) {
            $reflectionProperty = $reflectionClass->getProperty($property);

            if ($reflectionProperty->isPublic() && !$reflectionProperty->isStatic()) {
                $reflectionProperty->setValue($this, $value);
            }
        }
    }

    private function getCriteria(?string $q): ?array{
        if (!is_null($q)){
            $parameters = explode(';', $q);
            $criteria = [];
            foreach ($parameters as $parameter){
                $key = explode(':', $parameter)[0];
                $value = explode(':', $parameter)[1];
                $criteria[$key] = $value;
            }
            return $criteria;
        }
        else{
            return null;
        }
    }

    private function hasQProperty(): bool
    {
        $reflectionClass = new ReflectionClass($this);

        try {
            $property = $reflectionClass->getProperty('q');
            return $property->isPublic() && !$property->isStatic();
        } catch (\ReflectionException $e) {
            return false;
        }
    }
}
