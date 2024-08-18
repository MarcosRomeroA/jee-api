<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Symfony;

use App\Contexts\Shared\Domain\Exception\ValidationException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract readonly class BaseRequest
{
    /**
     * @throws ReflectionException
     */
    public function __construct(protected ValidatorInterface $validator)
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
        $request = Request::createFromGlobals();
        $q = $this->getCriteria($request->query->get('q'));

        try{
            $request = $request->toArray();
            if ($q){
                $request['q'] = $q;
            }
            return $request;
        }
        catch (\Exception){
            $request = [];
            if ($q){
                $request['q'] = $q;
            }
            return $request;
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
}