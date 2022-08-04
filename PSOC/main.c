#include "main.h"
#include "DHT_task.h"
#include "httpClient.h"

void interruptTemp(void *callback_arg, cyhal_timer_event_t event)
{
    //een interrupt bij button press die de HTTPSend functie laat lopen.
    xTaskResumeFromISR(httpSend_task_handle);
}

void softwareTimer(void *arg)
{
    for (;;)
    {
        vTaskDelay(pdMS_TO_TICKS(1800000));
        sensor_type = 1;
        vTaskResume(httpSend_task_handle);
        vTaskDelay(pdMS_TO_TICKS(1800000));
        sensor_type = 2;
        vTaskResume(httpSend_task_handle);
    }
}

void httpSend(void *pvParameters)
{
    cy_rslt_t result;
    cy_awsport_server_info_t serverInfo;
    (void)memset(&serverInfo, 0, sizeof(serverInfo));
    serverInfo.host_name = SERVERHOSTNAME;
    serverInfo.port = SERVERPORT;

    cy_http_disconnect_callback_t disconnectCallback = (void *)disconnect_callback;
    cy_http_client_t clientHandle;

    uint8_t buffer[BUFFERSIZE];
    cy_http_client_request_header_t request;
    request.buffer = buffer;
    request.buffer_len = BUFFERSIZE;
    request.method = CY_HTTP_CLIENT_METHOD_POST;
    request.range_start = -1;
    request.range_end = -1;
    request.resource_path = TESTPATH;

    uint8_t num_headers = 1;
    cy_http_client_header_t header[num_headers];
    header[0].field = "Content-Type";
    header[0].field_len = strlen("Content-Type");
    header[0].value = "application/x-www-form-urlencoded";
    header[0].value_len = strlen("application/x-www-form-urlencoded");

    result = cy_http_client_init();
    if (result != CY_RSLT_SUCCESS)
        printf("HTTP Client Library Initialization Failed!\n\r");

    result = cy_http_client_create(NULL, &serverInfo, disconnectCallback, NULL, &clientHandle);
    if (result != CY_RSLT_SUCCESS)
        printf("HTTP Client Creation Failed!\n\r");

    for (;;)
    {
        //suspending task and waiting for interrupt to resume
        vTaskSuspend(httpSend_task_handle);
        //reading sensorValues
        //die hier uw sensor uit lezen logica, roep gewoon functie op.
        result = cy_http_client_write_header(clientHandle, &request, header, num_headers);
        if (result != CY_RSLT_SUCCESS)
            printf("HTTP Client Header Write Failed!\n\r");

        result = cy_http_client_connect(clientHandle, SENDRECEIVETIMEOUT, SENDRECEIVETIMEOUT);
        if (result != CY_RSLT_SUCCESS)
            printf("HTTP Client Connection Failed!\n\r");
        else
            connected = true;

        char front[30] = "value=";

        strcat(front, value);
        strcat(front, end);
        strcat(front, "1");

        printf("request body: %s\r\n", front);
        sensor_type = 0;

        if (connected)
        {
            result = cy_http_client_send(clientHandle, &request, front, strlen(front), &response);
            if (result != CY_RSLT_SUCCESS)
                printf("HTTP Client Send Failed!\n\r");
            else
                printf("HTTP Client Send succes\n\r");
        }

        result = cy_http_client_disconnect(clientHandle);
        if (result != CY_RSLT_SUCCESS)
            printf("HTTP Client disconnect Failed!\n\r");
    }
}

void wifi_connect(void *arg)
{
    cy_rslt_t result;
    cy_wcm_connect_params_t connect_param;
    cy_wcm_ip_address_t ip_address;
    uint32_t retry_count;

    /* Configure the interface as a Wi-Fi STA (i.e. Client). */
    cy_wcm_config_t config = {.interface = CY_WCM_INTERFACE_TYPE_STA};

    /* Initialize the Wi-Fi Connection Manager and return if the operation fails. */
    result = cy_wcm_init(&config);
    if (result != CY_RSLT_SUCCESS)
        printf("\r\nWi-Fi Connection Manager initialization failed\r\n");
    else
        printf("\n\rWi-Fi Connection Manager initialized.\n\r\r");

    /* Configure the connection parameters for the Wi-Fi interface. */
    memset(&connect_param, 0, sizeof(cy_wcm_connect_params_t));
    memcpy(connect_param.ap_credentials.SSID, WIFI_SSID, sizeof(WIFI_SSID));
    memcpy(connect_param.ap_credentials.password, WIFI_PASSWORD, sizeof(WIFI_PASSWORD));
    connect_param.ap_credentials.security = WIFI_SECURITY;

    /* Connect to the Wi-Fi AP. */
    for (retry_count = 0; retry_count < MAX_WIFI_CONN_RETRIES; retry_count++)
    {
        printf("Connecting to Wi-Fi AP '%s'\n\r\r", connect_param.ap_credentials.SSID);
        result = cy_wcm_connect_ap(&connect_param, &ip_address);

        if (result != CY_RSLT_SUCCESS)
            continue;
        printf("Successfully connected to Wi-Fi network '%s'\n\r\r", connect_param.ap_credentials.SSID);
        if (ip_address.version == CY_WCM_IP_VER_V4)
            printf("My ipv4: %d.%d.%d.%d\n\r\r", (uint8_t)ip_address.ip.v4, (uint8_t)(ip_address.ip.v4 >> 8), (uint8_t)(ip_address.ip.v4 >> 16), (uint8_t)(ip_address.ip.v4 >> 24));
        else
            printf("My ipv6: %0X.%0X.%0X.%0X\n\r\r", (unsigned int)ip_address.ip.v6[0], (unsigned int)ip_address.ip.v6[1], (unsigned int)ip_address.ip.v6[2], (unsigned int)ip_address.ip.v6[3]);
        break;
    }
    if (result != CY_RSLT_SUCCESS)
    {
        printf("\r\nWifi connection failed.\r\n");
        CY_ASSERT(0);
    }
    else
    {
        while (1)
        {
            if (cy_wcm_is_connected_to_ap() != true)
            {
                cyhal_gpio_write(CYBSP_LED8, CYBSP_LED_STATE_ON);
                break;
            }
            vTaskDelay(10000);
        }
    }
}

int main(void)
{
    cybsp_init();

    cy_retarget_io_init(CYBSP_DEBUG_UART_TX, CYBSP_DEBUG_UART_RX, CY_RETARGET_IO_BAUDRATE);
    cyhal_gpio_init(CYBSP_LED8, CYHAL_GPIO_DIR_OUTPUT, CYHAL_GPIO_DRIVE_STRONG, CYBSP_LED_STATE_OFF);
    cyhal_gpio_init(CYBSP_LED_RGB_BLUE, CYHAL_GPIO_DIR_OUTPUT, CYHAL_GPIO_DRIVE_STRONG, CYBSP_LED_STATE_OFF);
    cyhal_gpio_init(CYBSP_SW2, CYHAL_GPIO_DIR_INPUT, CYHAL_GPIO_DRIVE_NONE, false);

    cyhal_gpio_register_callback(CYBSP_SW2, interrupt, NULL);
    cyhal_gpio_enable_event(CYBSP_SW2, CYHAL_GPIO_IRQ_FALL, 5, true);

    xTaskCreate(wifi_connect, "wifi_connect_task", 1024, NULL, 2, NULL);
    xTaskCreate(httpSend, "SendHttp_task", HTTP_CLIENT_TASK_STACK_SIZE, NULL, 5, &httpSend_task_handle);
    xTaskCreate(softwareTimer, "softwareTimer_task", 1024, NULL, 4, NULL);

    vTaskStartScheduler();
}

void disconnect_callback(void *arg)
{
    printf("Disconnected from HTTP Server\n\r");
}