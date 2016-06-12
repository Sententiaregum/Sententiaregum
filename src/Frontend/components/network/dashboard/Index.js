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

import React from 'react';
import Translate from 'react-translate-component';

/**
 * Dashboard page.
 *
 * @returns {React.Element} The dasboard markup.
 */
export default () => {
  return (
    <div>
      <h1><Translate content="pages.network.dashboard.index.title" /></h1>
    </div>
  );
};
