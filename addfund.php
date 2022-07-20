<form method="POST" action="request/add">
    <input type="hidden" name="token" value="yoursessiontoken">
    <div class="mb-3 text-center">
        <label> Method of payment</label>
        <select name="type">
            <option value="ETHEREUM">Ethereum</option>
            <option value="BITCOIN">Bitcoin</option>
            <option value="LITECOIN">Litecoin</option>
            <option value="MONERO">Monero</option>
            <option value="BITCOIN_CASH">Bitcoin Cash</option>
            <option value="NANO">Nano</option>
            <option value="SOLANA">Solana</option>
        </select>
        <input value="15" type="number" min="15" max="100" name="amount">
    </div>
    <button name="submit" type="submit"> Add Funds Now </button>
</form>