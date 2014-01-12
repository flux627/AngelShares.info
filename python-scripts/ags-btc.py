#!/usr/bin/python

from collections import defaultdict
import urllib
import json
import MySQLdb as mdb

# GLOBALS
ANGEL_ADDRESS = '1ANGELwQwWxMmbdaSWhWLqBEtPTkWb8uDc'
#con = mdb.connect('localhost', 'root', 'YoyoNessBat64', 'protoshares');

def get_txs(page=1):
    # Get transactions, optional page number.
    # Every page is supposed to show 50, however due to new transactions coming in between loads, 
    # this increments by 30 for safety.
    offset = (page - 1) * 30
    txs_get = urllib.urlopen('http://blockchain.info/rawaddr/{0}?offset={1}'.format(ANGEL_ADDRESS,offset))
    return json.load(txs_get)

def get_current_block():
    # Returns current BTC Blockheight
    return urllib.urlopen('https://blockchain.info/q/getblockcount').readline()

def get_block_time(blockheight):
    # Returns timestamp of given block's blockheight.
    block_get = urllib.urlopen('https://blockchain.info/block-height/{0}?format=json'.format(blockheight))
    blocks = block_get["blocks"]
    for block in blocks:
        if block["main_chain"] == True:
            return block["time"]

def get_total_donation(tx):
    # Returns total amount sent to Angel Address. If Angel Address does not receive funds
    # in this transaction, this returns False.
    for output in tx["out"]:
        addr = output["addr"]
        if addr == ANGEL_ADDRESS:
            return output["value"]
        else:
            continue
    return False

def get_last():
    # Looks up database for last transaction processed, returns.
    with con:
        cur = con.cursor()
        cur.execute("SELECT value FROM btc_control WHERE name = 'Last_Transaction_ID'")
        last_tx = cur.fetchone()
        return last_tx

def set_last(tx_id):
    # Sets database indicator for last transaction processed.
    with con:
        cur = con.cursor()
        cur.execute("UPDATE btc_control set value = {0}, time = now() where name = 'Last_Transaction_ID'".format(tx_id))

def find_last(txs_json,tx_id):
    # Searches transactions to find the last transaction processed, returns index.
    index = 0
    for tx in txs_json:
        if tx["hash"] == tx_id:
            return index
        else:
            index += 1


def prepare_data():
    # Prepare data to be inserted into database.
    txs_json = get_txs()
    current_block = get_current_block()
    n_txs = txs_json["n_tx"]
    txs_parsed = []
    stop = False
    first = True
    for tx in txs_json["txs"]:
        if first == True:
            set_last(tx["hash"])
        if stop == True:
            break
        donation = get_total_donation(tx)
        if donation == False:
            continue
        tx_id = tx["hash"]
        rel_addr = []
        for in_put in tx["inputs"]:
            addr = in_put["prev_out"]["addr"]
            rel_addr.append(addr)

        block = tx["block_height"]

        tx_set = { "tx": tx["hash"],
                   "addresses": list(set(rel_addr)),
                   "total_sent": donation,
                   "block": block }
        txs_parsed.append(tx_set)
    return txs_parsed

def insert_btc_addresses(tx_set):
    # Inserts (address, txid) to database 'btc_addresses'
    with con:
        cur = con.cursor()
        cur.execute("...")

def insert_btc_transactions(tx_set):
    # Inserts (address, txid) to database 'btc_addresses'
    with con:
        cur = con.cursor()
        cur.execute("...")

