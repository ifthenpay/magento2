<?php
/**
* Ifthenpay_Payment module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Payment
* @author      Ifthenpay
* @copyright   Ifthenpay (http://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

namespace Ifthenpay\Payment\Helper;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\App\Helper\Context;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Setup\SchemaSetupInterface;


class DataPaymentMethodTable extends AbstractHelper
{
   private $setup;
   private $connection;

    public function __construct(Context $context, SchemaSetupInterface $setup)
    {
        parent::__construct($context);
        $this->setup = $setup;
        $this->connection = $setup->getConnection();
    }

    private function createMultibancoTable(): void
    {
        if($this->connection->isTableExists('ifthenpay_multibanco') != true) {
            $table = $this->connection
            ->newTable($this->setup->getTable('ifthenpay_multibanco'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'entidade',
                Table::TYPE_TEXT,
                20,
                ['nullable' => false]
            )
            ->addColumn(
              'referencia',
              Table::TYPE_TEXT,
              13,
              ['nullable' => false]
            )
            ->addColumn(
                'order_id',
                Table::TYPE_TEXT,
                250,
              ['nullable' => false]
            )
            ->addColumn(
              'status',
              Table::TYPE_TEXT,
              50,
              ['nullable' => true]
            )
            ->addIndex(
                $this->setup->getIdxName('ifthenpay_multibanco', ['referencia']),
                ['referencia']
            );
            $this->connection->createTable($table);
        }
    }

    private function createMbwayTable(): void
    {
        if($this->connection->isTableExists('ifthenpay_mbway') != true) {
            $table = $this->connection
            ->newTable($this->setup->getTable('ifthenpay_mbway'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'id_transacao',
                Table::TYPE_TEXT,
                20,
                ['nullable' => false]
            )
            ->addColumn(
              'telemovel',
              Table::TYPE_TEXT,
              20,
              ['nullable' => false]
            )
            ->addColumn(
                'order_id',
                Table::TYPE_TEXT,
                250,
              ['nullable' => false]
            )
            ->addColumn(
              'status',
              Table::TYPE_TEXT,
              50,
              ['nullable' => true]
            )
            ->addIndex(
                $this->setup->getIdxName('ifthenpay_mbway', ['id_transacao']),
                ['id_transacao']
            );
            $this->connection->createTable($table);
        }
    }

    private function createPayshopTable(): void
    {
        if($this->connection->isTableExists('ifthenpay_payshop') != true) {
            $table = $this->connection
            ->newTable($this->setup->getTable('ifthenpay_payshop'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'id_transacao',
                Table::TYPE_TEXT,
                20,
                ['nullable' => false]
            )
            ->addColumn(
              'referencia',
              Table::TYPE_TEXT,
              13,
              ['nullable' => false]
            )
            ->addColumn(
              'validade',
              Table::TYPE_TEXT,
              8,
              ['nullable' => false]

            )
            ->addColumn(
                'order_id',
                Table::TYPE_TEXT,
                250,
              ['nullable' => false]
            )
            ->addColumn(
              'status',
              Table::TYPE_TEXT,
              50,
              ['nullable' => true]
            )
            ->addIndex(
                $this->setup->getIdxName('ifthenpay_payshop', ['id_transacao']),
                ['id_transacao']
            );
            $this->connection->createTable($table);
          }
    }

    private function createCCardTable(): void
    {
        if($this->connection->isTableExists('ifthenpay_ccard') != true) {
            $table = $this->connection
            ->newTable($this->setup->getTable('ifthenpay_ccard'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'requestId',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false]
            )
            ->addColumn(
              'paymentUrl',
              Table::TYPE_TEXT,
              1000,
              ['nullable' => false]
            )
            ->addColumn(
              'order_id',
              Table::TYPE_TEXT,
              250,
              ['nullable' => false]
            )
            ->addColumn(
              'status',
              Table::TYPE_TEXT,
              50,
              ['nullable' => true]
            )
            ->addIndex(
                $this->setup->getIdxName('ifthenpay_ccard', ['requestId']),
                ['requestId']
            );
            $this->connection->createTable($table);
        }

    }
    public function createDatabaseTables(array $userPaymentMethods) : void
    {
        $this->setup->startSetup();

        foreach ($userPaymentMethods as $paymentMethod) {
            switch ($paymentMethod) {
                case Gateway::MULTIBANCO:
                    $this->createMultibancoTable();
                    break;
                case Gateway::MBWAY:
                    $this->createMbwayTable();
                    break;
                case Gateway::PAYSHOP:
                    $this->createPayshopTable();
                    break;
                case Gateway::CCARD:
                    $this->createCCardTable();
                    break;
                default:
            }
        }

        $this->setup->endSetup();
    }
}
