/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React     from 'react';
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
