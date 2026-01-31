<?php

namespace ConvoChat\LaravelSmsGateway\Services;

class ConvoChatContactsService extends BaseConvoChatService
{
    public const CONTACTS_ENDPOINT = '/get/contacts';
    public const CREATE_CONTACT_ENDPOINT = '/create/contact';
    public const DELETE_CONTACT_ENDPOINT = '/delete/contact';
    public const GROUPS_ENDPOINT = '/get/groups';
    public const CREATE_GROUP_ENDPOINT = '/create/group';
    public const DELETE_GROUP_ENDPOINT = '/delete/group';
    public const UNSUBSCRIBED_ENDPOINT = '/get/unsubscribed';
    public const DELETE_UNSUBSCRIBED_ENDPOINT = '/delete/unsubscribed';

    protected function getServiceName(): string
    {
        return 'Contacts';
    }

    public function getContacts(array $filters = []): array
    {
        $data = array_merge($filters, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::CONTACTS_ENDPOINT, $data, 'GET');
    }

    public function createContact(array $params): array
    {
        $requiredParams = ['phone', 'name', 'groups'];
        $this->validateRequiredParams($params, $requiredParams);

        $data = array_merge($params, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::CREATE_CONTACT_ENDPOINT, $data);
    }

    public function deleteContact(int $contactId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'id' => $contactId,
        ];

        return $this->makeRequest(self::DELETE_CONTACT_ENDPOINT, $data, 'GET');
    }

    public function getGroups(array $filters = []): array
    {
        $data = array_merge($filters, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::GROUPS_ENDPOINT, $data, 'GET');
    }

    public function createGroup(array $params): array
    {
        $requiredParams = ['name'];
        $this->validateRequiredParams($params, $requiredParams);

        $data = array_merge($params, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::CREATE_GROUP_ENDPOINT, $data);
    }

    public function deleteGroup(int $groupId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'id' => $groupId,
        ];

        return $this->makeRequest(self::DELETE_GROUP_ENDPOINT, $data, 'GET');
    }

    public function getUnsubscribed(array $filters = []): array
    {
        $data = array_merge($filters, [
            'secret' => $this->apiKey,
        ]);

        return $this->makeRequest(self::UNSUBSCRIBED_ENDPOINT, $data, 'GET');
    }

    public function deleteUnsubscribed(int $contactId): array
    {
        $data = [
            'secret' => $this->apiKey,
            'id' => $contactId,
        ];

        return $this->makeRequest(self::DELETE_UNSUBSCRIBED_ENDPOINT, $data, 'GET');
    }
}
