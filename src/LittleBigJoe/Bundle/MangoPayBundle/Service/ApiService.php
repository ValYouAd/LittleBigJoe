<?php

namespace LittleBigJoe\Bundle\MangoPayBundle\Service;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Handle MangoPay
 */
class ApiService
{
		protected $container;
		protected $privateKeyFile;
		protected $privateKeyPassword;
				
		public function __construct($container)
		{
				$this->container = $container;
				$this->privateKeyFile = $this->container->getParameter('leetchi_private_key_file');
				$this->privateKeyPassword = $this->container->getParameter('leetchi_private_key_password');
		}
	
		/**
		 * Prettify JSON data
		 * 
		 * @param json $json
		 * @return json
		 */
		public function json_format($json)
		{
		    $tab = " ";
		    $new_json = "";
		    $indent_level = 0;
		    $in_string = false;
		
		    $json_obj = json_decode($json);
		
		    if($json_obj === false)
		        return false;
		
		    $json = json_encode($json_obj);
		    $len = strlen($json);
		
		    for($c = 0; $c < $len; $c++)
		    {
		        $char = $json[$c];
		        switch($char)
		        {
		            case '{':
		            case '[':
		                if(!$in_string)
		                {
		                    $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
		                    $indent_level++;
		                }
		                else
		                {
		                    $new_json .= $char;
		                }
		                break;
		            case '}':
		            case ']':
		                if(!$in_string)
		                {
		                    $indent_level--;
		                    $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
		                }
		                else
		                {
		                    $new_json .= $char;
		                }
		                break;
		            case ',':
		                if(!$in_string)
		                {
		                    $new_json .= ",\n" . str_repeat($tab, $indent_level);
		                }
		                else
		                {
		                    $new_json .= $char;
		                }
		                break;
		            case ':':
		                if(!$in_string)
		                {
		                    $new_json .= ": ";
		                }
		                else
		                {
		                    $new_json .= $char;
		                }
		                break;
		            case '"':
		                if($c > 0 && $json[$c-1] != '\\')
		                {
		                    $in_string = !$in_string;
		                }
		            default:
		                $new_json .= $char;
		                break;
		        }
		    }
		
		    return $new_json;
		}
	
		/**
		 * Sign HTTP request
		 * 
		 * @param string $httpMethod
		 * @param string $urlPath
		 * @param string $requestBody
		 * @return string
		 */
		public function createAuthSignature($httpMethod, $urlPath, $requestBody = "")
		{
				$data = "$httpMethod|$urlPath|";
				$privateKeyFile = $this->privateKeyFile;
				$privateKeyPassword = $this->privateKeyPassword;
				
				if ($httpMethod != "GET" && $httpMethod != "DELETE")
					$data .= "$requestBody|";
			
				$privateKey = openssl_pkey_get_private("file://$privateKeyFile", $privateKeyPassword);
				
				$signedData = null;
				openssl_sign($data, $signedData, $privateKey, OPENSSL_ALGO_SHA1);
				$signature = base64_encode($signedData);
				
				return $signature;
		}
		
		/**
		 * Format amount
		 *
		 * @param float $amount
		 * @return string
		 */
		public function formatAmount($amount)
		{
				return number_format($amount / 100.0, 2, ".", "");
		}

		/**
		 * Parse amount
		 *
		 * @param float $amount
		 * @return float
		 */
		public function parseAmount($amount)
		{
				return (int)round(floatval($amount) * 100);
		}
		
		/**
		 * Return Leetchi base URL
		 * 
		 * @return string
		 */
		public function getLeetchiBaseURL()
		{
				return trim($this->container->getParameter('leetchi_base_url'), '/');
		}
		
		/**
		 * Build request URL path
		 * 
		 * @param string $resourcePath
		 */
		public function buildRequestUrlPath($resourcePath)
		{
				$partnerID = $this->container->getParameter('leetchi_partner_id');
				$resourcePath = trim($resourcePath, '/');
			
				$url = "/v1/partner/$partnerID/$resourcePath";
			
				$findme = '?';
				$pos = strpos($resourcePath, $findme);
				if ($pos === false) 
				{
						$url = $url."?";
				} 
				else 
				{
						$url = $url."&";
				}
			
				return $url."ts=1379664401";
		}
		
		/**
		 * Make request
		 * 
		 * @param string $resourcePath
		 * @param string $method
		 * @param string $body		 
		 * @return mixed|boolean
		 */
		public function requestWithPrint($resourcePath, $method, $body = null) 
		{
				print("$method /$resourcePath\n");
			
				$requestUrlPath = $this->buildRequestUrlPath($resourcePath);
				$sign = $this->createAuthSignature($method, $requestUrlPath, $body);
				print("Signature : $sign\n");
				
				$leetchiBaseURL = $this->getLeetchiBaseURL();
				$url = $leetchiBaseURL.$requestUrlPath;
				print("Request : $url\n");
				print("Request data (Json format):\n");				
				print_r($this->json_format($body)."\n");
				
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
				if ($method == "POST") 
				{			
						curl_setopt($ch, CURLOPT_POST, true);
				}
			
				if ($method == "PUT") 
				{			
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				}
			
				if ($method == "DELETE") 
				{
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				}
			
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Leetchi-Signature: $sign", "Content-Type: application/json"));
				if ($body != null) 
				{
						curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
				}
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
				$data = curl_exec($ch);
				if (curl_errno($ch)) 
				{
						print('cURL error: '.curl_error($ch));
				} 
				else 
				{
						$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
						print("HTTP response code: $statusCode\n");
				}
				curl_close($ch);
				
				if ($data != false) 
				{
						print("Response data (Json format):\n");
						print_r($this->json_format($data));
						print("\n\n---------------\n");
						$result = json_decode($data);
						
						//print_r($result);
						if ($result != null) 
						{
								return $result;
						}
						print($data);
				}
				
				return false;
		}
		
		/**
		 * Make twith request
		 *
		 * @param string $resourcePath
		 * @param string $method
		 * @param string $body
		 * @param string $signature
		 * @return mixed|boolean
		 */
		public function requestWithSign($resourcePath, $method, $body = null, $signature)
		{
				print("$method /$resourcePath\n");
			
				$requestUrlPath = $this->buildRequestUrlPath($resourcePath);
				$sign = $signature;
				print("Signature : $sign\n");
				
				$leetchiBaseURL = $this->getLeetchiBaseURL();
				$url = $leetchiBaseURL.$requestUrlPath;
				print("request: $url\n");
				
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
				if ($method == "POST") 
				{			
						curl_setopt($ch, CURLOPT_POST, true);
				}
			
				if ($method == "DELETE") 
				{
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				}
			
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Leetchi-Signature: $sign", "Content-Type: application/json"));
				if ($body != null) 
				{
						curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
				}
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
				$data = curl_exec($ch);
				if (curl_errno($ch)) 
				{
						print('cURL error: '.curl_error($ch));
				} 
				else 
				{
						$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
						print("HTTP response code: $statusCode\n");
				}
				curl_close($ch);
				
				if ($data != false) 
				{
						print("response data:\n");
						$result = json_decode($data);
						
						//print_r($result);
						if ($result != null) 
						{
								return $result;
						}
						print($data);
				}
				
				return false;
		}
		
		/** 
		 * Make request without printing data
		 * 
		 * @param string $resourcePath
		 * @param string $method
		 * @param string $body
		 * @return mixed|boolean
		 */
		public function request($resourcePath, $method, $body = null) 
		{
				$requestUrlPath = $this->buildRequestUrlPath($resourcePath);
				$sign = $this->createAuthSignature($method, $requestUrlPath, $body);
				$leetchiBaseURL = $this->getLeetchiBaseURL();
				$url = $leetchiBaseURL.$requestUrlPath;
				
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
				if ($method == "POST") 
				{			
						curl_setopt($ch, CURLOPT_POST, true);
				}
			
				if ($method == "PUT") 
				{			
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				}
			
				if ($method == "DELETE") 
				{
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				}
			
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Leetchi-Signature: $sign", "Content-Type: application/json"));
				if ($body != null) 
				{
						curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
				}
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
				$data = curl_exec($ch);
				if (curl_errno($ch)) 
				{					
				} 
				else 
				{
						$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				}
				curl_close($ch);
				
				if ($data != false) 
				{
						$result = json_decode($data);
						
						if ($result != null) 
						{
								return $result;
						}
				}
				
				return false;
		}
		
		/** 
		 * Create user
		 * 
		 * @param string $email
		 * @param string $firstname
		 * @param string $lastname
		 * @param string $ip
		 * @param string $birthday
		 * @param string $nationality
		 * @param string $personType
		 * @param string $tag
		 * @param string $canRegisterMeanOfPayment
		 * @return NULL|mixed
		 */
		public function createUser($email, $firstname, $lastname, $ip, $birthday, $nationality, $personType = 'NATURAL_PERSON', $tag = null, $canRegisterMeanOfPayment = false)
		{
				// Get vars and convert format
				$body = json_encode(get_defined_vars());
				
				// Create user
				$user = $this->request("users", "POST", $body);
				
				if (!isset($user) || !isset($user->ID)) 
				{
						return null;
				}
				else
				{
						return $user;
				}
		}		
		
		/**
		 * Update user
		 *
		 * @param int $mangopayUserId
		 * @param string $email
		 * @param string $firstname
		 * @param string $lastname
		 * @param string $birthday
		 * @param string $nationality
		 * @param string $tag
		 * @param string $canRegisterMeanOfPayment
		 * @return NULL|mixed
		 */
		public function updateUser($mangopayUserId, $email, $firstname, $lastname, $birthday, $nationality, $tag = null, $canRegisterMeanOfPayment = false)
		{
				// Get vars and convert format
				$body = json_encode(get_defined_vars());
			
				$user = $this->request("users/".$mangopayUserId, "GET");
				if (!isset($user) || !isset($user->ID))
				{
						return null;
				}
				
				// Update user
				$user = $this->request("users/".$mangopayUserId, "PUT", $body);
			
				if (!isset($user) || !isset($user->ID))
				{
						return null;
				}
				else
				{
						return $user;
				}
		}
		
		/**
		 * Create project
		 *
		 * @param int $mangopayUserId
		 * @param array $owners
		 * @param string $tag
		 * @param string $name
		 * @param string $description
		 * @param string $raisingGoalAmount
		 * @param string $contributionLimitDate
		 * @return NULL|mixed
		 */
		public function createProject($mangopayUserId, $owners, $tag = null, $name = null, $description = null, $raisingGoalAmount = null, $contributionLimitDate = null)
		{
				// Get vars and convert format
				$body = json_encode(get_defined_vars());
					
				$user = $this->request("users/".$mangopayUserId, "GET");
				if (!isset($user) || !isset($user->ID))
				{
						return null;
				}
				
				// Create project
				$project = $this->request("wallets", "POST", $body);
				
				if (!isset($project) || !isset($project->ID))
				{
						return null;
				}
				else
				{
						return $project;
				}
		}
		
		/**
		 * Update project
		 *
		 * @param int $mangopayWalletId
		 * @param string $tag
		 * @param string $name
		 * @param string $description
		 * @param string $raisingGoalAmount
		 * @param string $contributionLimitDate
		 * @return NULL|mixed
		 */
		public function updateProject($mangopayWalletId, $tag = null, $name = null, $description = null, $raisingGoalAmount = null, $contributionLimitDate = null)
		{
				// Get vars and convert format
				$body = json_encode(get_defined_vars());
				
				$project = $this->request("wallets/".$mangopayWalletId, "GET");
				if (!isset($project) || !isset($project->ID))
				{
						return null;
				}
				
				// Update project
				$project = $this->request("wallets/".$mangopayWalletId, "PUT", $body);
				
				if (!isset($project) || !isset($project->ID))
				{
						return null;
				}
				else
				{
						return $project;
				}
		}
		
		/**
		 * Create contribution
		 *
		 * @param int $mangopayWalletId
		 * @param int $mangopayUserId
		 * @param float $amount
		 * @param string $returnUrl
		 * @param string $tag
		 * @param float $clientFeeAmount
		 * @param string $templateURL
		 * @param boolean $registerMeanOfPayment
		 * @param int $paymentCardID
		 * @param string $culture
		 * @param string $paymentMethodType
		 * @param string $type
		 * @return NULL|mixed
		 */
		public function createContribution($mangopayWalletId, $mangopayUserId, $amount, $returnUrl, $tag = null, $clientFeeAmount = null, $templateURL = null, $registerMeanOfPayment = null, $paymentCardID = null, $culture = null, $paymentMethodType = null, $type = null)
		{
				// Get vars and convert format
				$data = get_defined_vars();
				$data['userID'] = $mangopayUserId;
				$data['walletID'] = $mangopayWalletId;
				$body = json_encode($data);
					
				$user = $this->request("users/".$mangopayUserId, "GET");
				if (!isset($user) || !isset($user->ID))
				{
						return null;
				}
			
				$project = $this->request("wallets/".$mangopayWalletId, "GET");
				if (!isset($project) || !isset($project->ID))
				{
						return null;
				}
				
				// Create contribution
				$contribution = $this->request("contributions", "POST", $body);

				if (!isset($contribution) || !isset($contribution->ID))
				{
						return null;
				}
				else
				{
						return $contribution;
				}
		}
		
		/**
		 * Fetch contribution
		 *
		 * @param int $mangopayContributionId
		 * @return NULL|mixed
		 */
		public function fetchContribution($mangopayContributionId)
		{
				// Fetch contribution
				$contribution = $this->request("contributions/".$mangopayContributionId, "GET");
					
				if (!isset($contribution) || !isset($contribution->ID))
				{
						return null;
				}
				else
				{
						return $contribution;
				}
		}
		
		/**
		 * Create refund
		 *
		 * @param int $contributionId
		 * @param int $userId
		 * @param string $tag
		 * @return NULL|mixed
		 */
		public function createRefund($contributionId, $userId, $tag = null)
		{
				// Get vars and convert format
				$body = json_encode(get_defined_vars());
				
				// Create refund
				$refund = $this->request("refunds", "POST", $body);
			
				if (!isset($refund) || !isset($refund->ID))
				{
						return null;
				}
				else
				{
						return $refund;
				}
		}
		
		/**
		 * Fetch operations
		 *
		 * @param int $walletId
		 * @return NULL|mixed
		 */
		public function fetchOperations($walletId)
		{
				// Fetch operations
				$operations = $this->request("wallets/".$walletId."/operations", "GET");
					
				if (empty($operations))
				{
						return null;
				}
				else
				{
						return $operations;
				}
		}
		
		/**
		 * Fetch operations
		 *
		 * @param int $walletId
		 * @return NULL|mixed
		 */
		public function listUsers($walletId, $include = null)
		{
				// List users
				$users = $this->request("wallets/".$walletId."/users".(($include) ? '?include="'.$include.'"' : ''), "GET");
					
				if (empty($users))
				{
						return null;
				}
				else
				{
						return $users;
				}
		}
		
		/**
		 * Create beneficiary
		 *
		 * @param string $bankAccountOwnerName
		 * @param string $bankAccountOwnerAddress
		 * @param string $bankAccountIban
		 * @param string $bankAccountBic
		 * @param string $tag
		 * @param integer $userId
		 * @return NULL|mixed
		 */
		public function createBeneficiary($bankAccountOwnerName, $bankAccountOwnerAddress, $bankAccountIban, $bankAccountBic, $tag = null, $userId = null)
		{
				// Get vars and convert format
				$body = json_encode(get_defined_vars());
			
				// Create beneficiary
				$beneficiary = $this->request("beneficiaries", "POST", $body);
			
				if (!isset($beneficiary) || !isset($beneficiary->ID))
				{
						return null;
				}
				else
				{
						return $beneficiary;
				}
		}
		
		/**
		 * Create withdrawal
		 *
		 * @param integer $userId
		 * @param integer $walletId
		 * @param integer $beneficiaryId
		 * @param float $amount
		 * @param float $clientFeeAmount
		 * @param string $tag
		 * @return NULL|mixed
		 */
		public function createWithdrawal($userId, $walletId, $beneficiaryId, $amount, $clientFeeAmount = null, $tag = null)
		{
				// Get vars and convert format
				$body = json_encode(get_defined_vars());
					
				// Create withdrawal
				$withdrawal = $this->request("withdrawals", "POST", $body);
					
				if (!isset($withdrawal) || !isset($withdrawal->ID))
				{
						return null;
				}
				else
				{
						return $withdrawal;
				}
		}
}
