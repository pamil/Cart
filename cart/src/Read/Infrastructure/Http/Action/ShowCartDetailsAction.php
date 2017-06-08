<?php

declare(strict_types=1);

namespace Pamil\Cart\Read\Infrastructure\Http\Action;

use Pamil\Cart\Read\Application\Repository\CartRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ShowCartDetailsAction
{
    /** @var CartRepository */
    private $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function __invoke(string $cartId): Response
    {
        $cart = $this->cartRepository->get($cartId);

        return JsonResponse::fromJsonString(json_encode($cart), 200);
    }
}
