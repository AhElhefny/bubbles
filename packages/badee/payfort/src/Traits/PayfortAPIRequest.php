<?php

namespace BadeePayfort\Traits;

use BadeePayfort\Exceptions\PayfortException;
use BadeePayfort\Exceptions\PayfortRequestException;


trait PayfortAPIRequest
{
    /**
     * Make Payfort create mobile sdk token request & return response.
     *
     * @see https://docs.payfort.com/docs/mobile-sdk/build/index.html#create-sdk-token
     *
     * @param string $deviceId The device id generated by payfort SDK to create SDK token to be used on it.
     * @return string Payfort sdk token
     *
     * @throws \BadeePayfort\Exceptions\PayfortRequestException
     */
    public function createMobileSDKToken($deviceId)
    {
        $params = [
            'service_command' => 'SDK_TOKEN',
            'access_code' => $this->config['access_code'],
            'merchant_identifier' => $this->config['merchant_identifier'],
            'language' => $this->config['language'],
            'device_id' => $deviceId
        ];

        $response = $this->callPayfortAPI($params);

        /*
         * According to payfort documentation
         * 22 refers to SDK Token creation success.
         * @see https://docs.payfort.com/docs/in-common/build/index.html#statuses
         */
        if ($response->status != '22') {
            throw new PayfortRequestException($response->response_message);
        }

        /*
         * According to payfort documentation
         * 22  refers to SDK Token creation success.
         * 000 refers to success message
         * @see https://docs.payfort.com/docs/in-common/build/index.html#messages
         */
        if ($response->response_code != '22000') {
            throw new PayfortRequestException($response->response_message);
        }

        # return SDK token only
        return $response->sdk_token;
    }

    /**
     * Make Payfort check status request & return response.
     *
     * @see https://docs.payfort.com/docs/in-common/build/index.html#check-status
     *
     * @param int $fortId The Payfort reference to check for its transactions status
     * @return \stdClass
     *
     * @throws \BadeePayfort\Exceptions\PayfortRequestException
     */
    public function checkOrderStatusByFortId($fortId)
    {
        $response = $this->checkOrderStatus([
            'fort_id' => $fortId
        ]);

        return $response;
    }

    /**
     * Make Payfort check status request & return response.
     *
     * @see https://docs.payfort.com/docs/in-common/build/index.html#check-status
     *
     * @param string $merchant_reference The Merchant reference to check for its transactions status
     * @return \stdClass
     *
     * @throws \BadeePayfort\Exceptions\PayfortRequestException
     */
    public function checkOrderStatusByMerchantReference($merchant_reference)
    {
        return $this->checkOrderStatus([
            'merchant_reference' => $merchant_reference
        ]);
    }

    /**
     * Make Payfort check status request & return response.
     *
     * @see https://docs.payfort.com/docs/in-common/build/index.html#check-status
     *
     * @param array $data The request parameters for check status request
     * @return \stdClass
     *
     * @throws \BadeePayfort\Exceptions\PayfortRequestException
     */
    private function checkOrderStatus($data)
    {
        $data = array_merge($data, [
            'query_command' => 'CHECK_STATUS',
            'access_code' => $this->config['access_code'],
            'merchant_identifier' => $this->config['merchant_identifier'],
            'language' => $this->config['language']
        ]);

        $response = $this->callPayfortAPI($data);

        /*
         * According to payfort documentation
         * 12 refers to Check Status success.
         * @see https://docs.payfort.com/docs/in-common/build/index.html#statuses
         */
        if ($response->status != '12') {
            throw new PayfortRequestException($response->response_message);
        }

        /*
        * According to payfort documentation
        * 12  refers to Check Status success.
        * 000 refers to success message
        * @see https://docs.payfort.com/docs/in-common/build/index.html#statuses
        */
        if ($response->response_code != '12000') {
            throw new PayfortRequestException($response->response_message);
        }
    }


    /**
     * Make Payfort Http request & return response.
     *
     * @param array $data Request parameters
     * @return \stdClass
     *
     * @throws \BadeePayfort\Exceptions\PayfortException
     */
    private function callPayfortAPI($data)
    {
        # Add payfort request signature to request data
        $data['signature'] = $this->calcPayfortSignature($data, 'request');

        try {
            # Make http request
            $rawResponse = $this->httpClient->post($this->payfortEndpoint, [
                'json' => $data
            ])->getBody();

            $response = json_decode($rawResponse);

            if (data_get($response, 'status') == '00') {
                throw new PayfortException(data_get($response, 'response_message'));
            }

            # Verify response signature
            if (data_get($response, 'signature') != $this->calcPayfortSignature(((array)$response), 'response')) {
                throw new PayfortException('Payfort response signature mismatched');
            }

            return $response;

        } catch (\Exception $e) {
            throw new PayfortException($e);
        }
    }
}
