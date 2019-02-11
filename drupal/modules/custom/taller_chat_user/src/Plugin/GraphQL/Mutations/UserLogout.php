<?php

namespace Drupal\taller_chat_user\Plugin\GraphQL\Mutations;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Flood\FloodInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManager;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\user\UserAuthInterface;
use Drupal\user\UserInterface;
use Drupal\graphql\Plugin\GraphQL\Mutations\MutationPluginBase;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Youshido\GraphQL\Execution\ResolveInfo;

/**
 * Logout User.
 *
 * @GraphQLMutation(
 *   id = "channel_user_logout",
 *   name = "userLogout",
 *   type = "Boolean",
 *   secure = false,
 *   nullable = false,
 *   schema_cache_tags = {"channel_user_logout"},
 *   arguments = {
 *     "uid" = "Int"
 *   }
 * )
 */
class UserLogout extends MutationPluginBase implements ContainerFactoryPluginInterface {
  use DependencySerializationTrait;
  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user service.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;

  /**
   * @var 
   */
  protected $sessionManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $pluginId,
    $pluginDefinition,
    EntityTypeManagerInterface $entityTypeManager,
    AccountInterface $currentUser,
    ModuleHandler $moduleHandler,
    SessionManager $sessionManager
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $currentUser;
    $this->moduleHandler = $moduleHandler;
    $this->sessionManager = $sessionManager;

    parent::__construct($configuration, $pluginId, $pluginDefinition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition) {
    return new static(
      $configuration,
      $pluginId,
      $pluginDefinition,
      $container->get('entity_type.manager'),
      $container->get('current_user'),
      $container->get('module_handler'),
      $container->get('session_manager')
    );
  }

  /**
   * Logs out a user.
   *
   * @return \Drupal\user\UserInterface
   *   The newly logged user.
   */
  public function resolve($value, array $args, ResolveInfo $info) {
    return $this->logout(\Drupal::request(), $args);
  }

  /**
   * Logs out a user.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return bool
   *   The newly logged user.
   */
  public function logout(Request $request, $args) {
    if (!$this->currentUser->isAuthenticated()) {
      throw new BadRequestHttpException($this->t('There is no logged in user.'));
    }

    $this->moduleHandler->invokeAll('user_logout', [$this->currentUser]);
    $this->sessionManager->destroy();

    return true;
  }
  
}
