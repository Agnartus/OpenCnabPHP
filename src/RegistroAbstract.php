<?php
/*
* classe ARecord
* Esta classe prov� os m�todos necess�rios para persistir e
* recuperar objetos da Array (Active Record)
*
* @author   Rogerio Muniz de Castro <rogerio@ciatec.net>
* @version  0.1
* @access   restrict
* 
* 2016.04.02 -- cria��o
**/
namespace rctnet;

abstract class RegistroAbstract
{
	protected $data; // array contendo os dados do objeto
	protected $meta;

	/* m�todo __construct()
	* instancia um Active Record. Se passado o $id, j� carrega o objeto
	* @param [$id] = ID do objeto
	*/
	public function __construct($arrayKeys = NULL)
	{
		if ($arrayKeys) // se o ID for informado
		{
			// carrega o objeto correspondente
			$object = $this->load($arrayKeys);
			if ($object)
			{
				$this->fromArray($object->toArray());
			}
		}
	}


	/*
	* m�todo __set()
	* executado sempre que uma propriedade for atribu�da.
	*/
	public function __set($prop, $value)
	{
		// verifica se existe m�todo set_<propriedade>
		if (method_exists($this, 'set_'.$prop))
		{
			// executa o m�todo set_<propriedade>
			call_user_func(array($this, 'set_'.$prop), $value);
		}
		else
		{

			if ($value === NULL)
			{
				unset($this->data[$prop]);
			}
			else
			{
				// atribui o valor da propriedade
				$this->data[$prop] = $value;
			}
		}
	}

	/*
	* m�todo __get()
	* executado sempre que uma propriedade for requerida
	*/
	public function __get($prop)
	{
		// verifica se existe m�todo get_<propriedade>
		if (method_exists($this, 'get_'.$prop))
		{
			// executa o m�todo get_<propriedade>
			return call_user_func(array($this, 'get_'.$prop));
		}
		else
		{
			// retorna o valor da propriedade
			if (isset($this->data[$prop]))
			{
				$metaData = (isset($this->meta[$prop]))?$this->meta[$prop]:null;
				switch ($metaData['tipo']) {
					case 'decimal':
						return ($this->data[$prop])?number_format($this->data[$prop],$metaData['precision'],'',''):'';
						break;
					case 'date':
						return ($this->data[$prop])?date("dmy",strtotime($this->data[$prop])):'';
						break;
					default:
						return $this->data[$prop];
						break;
				}

			}
		}
	}
	/*
	* m�todo getUnformated()
	* busca o valor de dentro do campo dentro do objeto de forma simples sem formata��o de valor por exemplo
	*/
	public function getUnformated($prop)
	{
		// retorna o valor da propriedade
		if (isset($this->data[$prop]))
		{
			return $this->data[$prop];
		}
	}

	/*
	* m�todo set_meta()
	* executado quando o objeto for clonado.
	* limpa o ID para que seja gerado um novo ID para o clone.
	*/
	public function set_meta($meta)
	{
		$this->meta = $meta;
	}
	/*
	* m�todo get_meta()
	* executado quando o objeto for clonado.
	* limpa o ID para que seja gerado um novo ID para o clone.
	*/
	public function get_meta()
	{
		return $this->meta;
	}


	/*
	* m�todo fromArray
	* preenche os dados do objeto com um array
	*/
	public function fromArray($data)
	{
		$this->data = $data;
	}

	/*
	* m�todo toArray
	* retorna os dados do objeto como array
	*/
	public function toArray()
	{
		return $this->data;
	}
}
?>