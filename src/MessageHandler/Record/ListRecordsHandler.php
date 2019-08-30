<?php
namespace App\MessageHandler\Record;

use App\DTO\Record\ListSearchCriteria;
use App\Entity\Exercise;
use App\Entity\User;
use App\Message\Record\ListRecords;
use App\Repository\RecordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ListRecordsHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private RecordRepository $recordRepository;

    public function __construct(EntityManagerInterface $em, RecordRepository $recordRepository)
    {
        $this->em = $em;
        $this->recordRepository = $recordRepository;
    }

    public function __invoke(ListRecords $listRecords): array
    {
        $user = $this->em->getReference(
            User::class,
            $listRecords->getAuthUser()->getId(),
        );

        $pagination = $listRecords->getPagination();
        $searchCriteria = new ListSearchCriteria($user, $pagination);

        $pagination->setTotalItems($this->recordRepository->getRecordsCount($searchCriteria));

        if ($listRecords->exercise) {
            $searchCriteria->exercise = $this->em->getReference(
                Exercise::class,
                Uuid::fromString($listRecords->exercise),
            );
        }

        return $this->recordRepository->getRecords($searchCriteria);
    }
}
