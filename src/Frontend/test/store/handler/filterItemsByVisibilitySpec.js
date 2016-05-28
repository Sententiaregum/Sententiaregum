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

import { expect } from 'chai';
import filterItemsByVisibility from '../../../store/handler/filterItemsByVisibility';

describe('filterItemsByVisibility', () => {
  const exampleData = [
    {
      id:        'protected item',
      role:      'ROLE_ADMIN',
      logged_in: true
    },
    {
      id:     'public item',
      portal: true
    },
    {
      id:        'internal',
      logged_in: true
    }
  ];

  it('filters everything for insufficient credentials', () => {
    const result = filterItemsByVisibility(exampleData, { is_admin: false, logged_in: false});

    expect(result[0].id).to.equal('public item');
    expect(result.length).to.equal(1);
  });

  it('filters everything for authenticated users', () => {
    const result = filterItemsByVisibility(exampleData, { is_admin: false, logged_in: true });

    expect(result.length).to.equal(1);
    expect(result[0].id).to.equal('internal');
  });

  it('filters everything for authenticated users with admin rights', () => {
    const result = filterItemsByVisibility(exampleData, { is_admin: true, logged_in: true });

    expect(result.length).to.equal(2);
    expect(result[0].id).to.equal('protected item');
    expect(result[1].id).to.equal('internal');
  });
});
