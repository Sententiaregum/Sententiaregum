/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import { store } from 'sententiaregum-flux-container';

export default store({
  CREATE_ACCOUNT: {
    params:   [],
    function: () => null
  },
  ACCOUNT_VALIDATION_ERROR: {
    params:   ['errors', 'nameSuggestions'],
    function: (errors, suggestions) => ({ suggestions, errors })
  }
});
